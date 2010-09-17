<?php
/**
 * Login view for janrain module
 * @copyright Copyright (C) 2010 - Jerome Vieilledent. All rights reserved
 * @licence http://www.gnu.org/licenses/gpl-2.0.txt GNU GPLv2
 * @author Jerome Vieilledent
 * @version @@@VERSION@@@
 * @package janrainauth
 */

$Module = $Params['Module'];
$http = eZHTTPTool::instance();
$Result = array();

try
{
    $auth = new JanrainAuth();
    $token = $http->postVariable( 'token' );
    
    // This may throw a JanrainAuthException if an auth error occurs
    // Might also throw other exceptions if communication with Janrain service is broken
    $authInfo = $auth->getAuthInfo( $token );
    $user = $auth->getUser( $authInfo );
    eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
    
    $redirectURI = '/';
    if ( $http->hasSessionVariable( 'LastAccessesURI' ) )
        $redirectURI = $http->sessionVariable( 'LastAccessesURI' );
    $Module->redirectTo( $redirectURI );
}
catch( Exception $e )
{
    $errMsg = $e->getMessage();
    eZDebug::writeError( $errMsg, 'JanrainAuth' );
    if ( $e instanceof JanrainAuthException )
        $errType = eZError::KERNEL_ACCESS_DENIED;
    else
        $errType = eZError::KERNEL_NOT_AVAILABLE;
    
    return $Module->handleError( $errType, 'kernel' );
}
