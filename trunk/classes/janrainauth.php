<?php
/**
 * JanrainAuth
 * @copyright Copyright (C) 2010 - Jerome Vieilledent. All rights reserved
 * @licence http://www.gnu.org/licenses/gpl-2.0.txt GNU GPLv2
 * @author Jerome Vieilledent
 * @version @@@VERSION@@@
 * @package janrainauth
 */
class JanrainAuth
{
    /**
     * @var eZHTTPTool
     */
    protected $http;
    
    /**
     * @var eZINI
     */
    protected $janrainINI;
    
    public function __construct()
    {
        $this->http = eZHTTPTool::instance();
        $this->janrainINI = eZINI::instance( 'janrain.ini' );
    }
    
    /**
     * Get Auth infos from token returned by Janrain
     * @param string $token Token returned by Janrain
     * @throws RuntimeException
     * @throws JanrainAuthException
     */
    public function getAuthInfo( $token )
    {
        if( !extension_loaded( 'curl' ) )
            throw new RuntimeException( 'CURL extension is not loaded !' );
        
        $postData = array(
            'token'     => $token,
            'apiKey'    => $this->janrainINI->variable( 'GeneralSettings', 'APIKey' ),
            'format'    => 'json'
        );
        
        $ini = eZINI::instance();
        $proxy = $ini->hasVariable( 'ProxySettings', 'ProxyServer' ) ? $ini->variable( 'ProxySettings', 'ProxyServer' ) : false;
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_URL, $this->janrainINI->variable( 'GeneralSettings', 'AuthInfoURL' ) );
        curl_setopt( $curl, CURLOPT_POST, true );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, $postData );
        curl_setopt( $curl, CURLOPT_HEADER, false );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
        // If we should use proxy
        if ( $proxy )
        {
            curl_setopt ( $ch, CURLOPT_PROXY , $proxy );
            $userName = $ini->hasVariable( 'ProxySettings', 'User' ) ? $ini->variable( 'ProxySettings', 'User' ) : false;
            $password = $ini->hasVariable( 'ProxySettings', 'Password' ) ? $ini->variable( 'ProxySettings', 'Password' ) : false;
            if ( $userName )
            {
                curl_setopt ( $ch, CURLOPT_PROXYUSERPWD, "$userName:$password" );
            }
        }
        
        $rawJSON = curl_exec( $curl );
        if( $rawJSON === false )
        {
            $errMsg = curl_error( $curl );
            curl_close( $curl );
            throw new RuntimeException( "An error occurred while exchangin data with Janrain server : '$errMsg'" );
        }
        curl_close( $curl );
        
        $authInfo = json_decode( $rawJSON );
        if( $authInfo->stat != 'ok' )
            throw new JanrainAuthException( 'An error occurred with user authentication : "'.$authInfo->err->msg.'"' );
        
        return $authInfo;
    }
    
    /**
     * Returns a valid eZUser identified by $authInfo, previously returned by Janrain
     * @param  stdClass $authInfo
     * @throws RuntimeException
     * @throws JanrainAuthException
     */
    public function getUser( stdClass $authInfo )
    {
        $userRemoteID = 'janrain|'.$authInfo->profile->identifier;
        $ini = eZINI::instance();
        $db = eZDB::instance();
        if( isset( $authInfo->profile->email ) )
        {
            $user = eZUser::fetchByEmail( $authInfo->profile->email );
            // If we have a user matched by its email and do not come from Janrain
            // Then return it as it should not be modified by Janrain info (internal user)
            if ( $user instanceof eZUser )
            {
                $existingUserRemoteID = $user->contentObject()->attribute( 'remote_id' );
                if( stripos( $userRemoteID, 'janrain' ) === false ) // User not from Janrain, return it
                {
                    return $user;
                }
                else // User registered through Janrain Auth
                {
                    if( $existingUserRemoteID != $userRemoteID ) // Same email but different authentication service provider
                    {
                        // TODO : Let the administrator to set a policy in that case in janrain.ini
                        // The user might be modified as below, but could also be rejected as email is already present in DB
                        $db->begin();
                        $user->contentObject()->setAttribute( 'remote_id', $userRemoteID );
                        $user->contentObject()->store( array( 'remote_id' ) );
                        $user->setAttribute( 'login', $authInfo->profile->identifier );
                        $user->store( array( 'login' ) );
                        $db->commit();
                    }
                }
            }
        }
        
        $classID = $ini->variable( 'UserSettings', 'UserClassID' );
        $userPlacement = SQLILocation::fromNodeID( $ini->variable( 'UserSettings', 'DefaultUserPlacement' ) );
        
        // Edit or create user
        $userOptions = new SQLIContentOptions( array(
            'class_identifier'      => eZContentClass::classIdentifierByID( $classID ),
            'creator_id'            => $ini->variable( 'UserSettings', 'UserCreatorID' ),
            'remote_id'             => $userRemoteID
        ) );
        $userContent = SQLIContent::create( $userOptions );
        $userContent->addLocation( $userPlacement );
        $activeLanguage = $userContent->fields->getActiveLanguage();
        
        // Loop against user content fields
        // If field is ezuser, edit it
        // Check if a mapping has been defined in janrain.ini between profile data field and content field
        foreach( $userContent->fields[$activeLanguage] as $attrIdentifier => $field )
        {
            if( $field->data_type_string == 'ezuser' ) // User datatype => Edit login/email
            {
                $login = $authInfo->profile->identifier;
                $email = isset( $authInfo->profile->email ) ? $authInfo->profile->email : '';
                $field->setData( $login.'|'.$email );
                continue;
            }
            
            $authInfoMap = $this->janrainINI->variable( 'UserSettings', 'AuthInfoMap' );
            if( isset( $authInfoMap[$attrIdentifier] ) ) // Does a mapping exist for this attribute ?
            {
                $profileDataField = $authInfoMap[$attrIdentifier];
                $dataFieldExists = false;
                if( strpos( $profileDataField, '/' ) !== false )
                {
                    list( $fieldPart1, $fieldPart2 ) = explode( '/', $profileDataField, 2 );
                    $dataFieldExists = isset( $authInfo->profile->$fieldPart1->$fieldPart2 );
                    $userData = $dataFieldExists ? $authInfo->profile->$fieldPart1->$fieldPart2 : null;
                }
                else
                {
                    $dataFieldExists = isset( $authInfo->profile->$profileDataField );
                    $userData = $dataFieldExists ? $authInfo->profile->$profileDataField : null;
                }
                
                if( $dataFieldExists ) // OK, now check if demanded profile field exists in data returned by Janrain
                {
                    switch( $field->data_type_string ) // Quick check by datatype (basically for photo/avatar as they need to be downloaded)
                    {
                        case 'ezimage':
                            $fieldContent = SQLIContentUtils::getRemoteFile( $userData );
                            break;
                            
                        default:
                            $fieldContent = $userData;
                    }
                    $field->setData( $fieldContent );
                }
            }
        }
        
        $publisher = SQLIContentPublisher::getInstance();
        $publisher->publish( $userContent );
        $user = eZUser::fetchByName( $authInfo->profile->identifier );
        if ( !$user instanceof eZUser )
            throw new RuntimeException( __METHOD__ . ' => An error occurred while fetching newly created user. Auth info : '.var_export( $authInfo, true ) );
        
        // Non-activated user should not be able to login
        if( !$user->attribute( 'is_enabled' ) )
            throw new JanrainAuthException( 'User '.$authInfo->profile->identifier.' is not activated. Auth info : '.var_export( $authInfo, true ) );
        
        return $user;
    }
}