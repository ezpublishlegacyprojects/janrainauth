{def $applicationDomain = ezini( 'GeneralSettings', 'ApplicationDomain', 'janrain.ini' )
     $siteURL = ezini( 'SiteSettings', 'SiteURL' )
     $securedURL = ezini( 'GeneralSettings', 'SecuredURL', 'janrain.ini' )
     $protocol = cond( $securedURL|eq( 'enabled' ), 'https', 'http' )
     $width = ezini( 'EmbedSettings', 'Width', 'janrain.ini' )
     $height = ezini( 'EmbedSettings', 'Height', 'janrain.ini' )
     $currentLocaleCode = fetch( 'content', 'locale' ).locale_code
     $languageMap = ezini( 'LanguageSettings', 'LanguageMap', 'janrain.ini' )
     $languageCode = ezini( 'LanguageSettings', 'DefaultLanguage', 'janrain.ini' )
     $defaultProvider = ezini( 'GeneralSettings', 'DefaultProvider', 'janrain.ini' )
     $tokenURI = ezini( 'GeneralSettings', 'TokenURI', 'janrain.ini' )}
{if is_set( $languageMap[$currentLocaleCode] )}{set $languageCode = $languageMap[$currentLocaleCode]}{/if}
<iframe id="janrain_loginframe" src="{$protocol}://{$applicationDomain}/openid/embed?token_url={$protocol}%3A%2F%2F{$siteURL}{$tokenURI|urlencode}&amp;language_preference={$languageCode}" scrolling="no" frameBorder="no" allowtransparency="true" style="width:{$width}px;height:{$height}px"></iframe>