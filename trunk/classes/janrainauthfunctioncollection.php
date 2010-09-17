<?php
/**
 * Janrain module fetch function collection
 * @copyright Copyright (C) 2010 - Jerome Vieilledent. All rights reserved
 * @licence http://www.gnu.org/licenses/gpl-2.0.txt GNU GPLv2
 * @author Jerome Vieilledent
 * @version @@@VERSION@@@
 * @package janrainauth
 */
class JanrainAuthFunctionCollection
{
    /**
     * Returns Janrain token full URL
     * @return string
     */
    public static function getTokenURL()
    {
        $janrainINI = eZINI::instance( 'janrain.ini' );
        $ini = eZINI::instance();
        
        $siteURL = $ini->variable( 'SiteSettings', 'SiteURL' );
        $httpProtocol = $janrainINI->variable( 'GeneralSettings', 'SecuredURL' ) === 'enabled' ? 'https://' : 'http://';
        $tokenURI = $janrainINI->variable( 'GeneralSettings', 'TokenURI' );
        $tokenFullURL = $httpProtocol.$siteURL.$tokenURI;
        
        return array( 'result' => $tokenFullURL);
    }
    
    /**
     * Returns Janrain signin full URL
     * @return string
     */
    public static function getSignInURL()
    {
        $janrainINI = eZINI::instance( 'janrain.ini' );
        $ini = eZINI::instance();
        $siteURL = $ini->variable( 'SiteSettings', 'SiteURL' );
        $httpProtocol = $janrainINI->variable( 'GeneralSettings', 'SecuredURL' ) === 'enabled' ? 'https://' : 'http://';
        $applicationDomain = $janrainINI->variable( 'GeneralSettings', 'ApplicationDomain' );
        $tokenURI = $janrainINI->variable( 'GeneralSettings', 'TokenURI' );
        $tokenFullURL = $httpProtocol.$siteURL.$tokenURI;

        $signInURI = $janrainINI->variable( 'ModalSettings', 'SigninURI' );
        $signInFullURL = $httpProtocol.$applicationDomain.$signInURI.'token_url='.rawurlencode( $tokenFullURL );
        
        return array( 'result' => $signInFullURL );
    }
}