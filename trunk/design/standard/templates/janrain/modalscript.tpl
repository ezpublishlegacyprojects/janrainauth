{*ezscript_require( 'ezjsc::jquery' )*}
{def $applicationDomain = ezini( 'GeneralSettings', 'ApplicationDomain', 'janrain.ini' )
     $siteURL = ezini( 'SiteSettings', 'SiteURL' )
     $securedTokenURL = ezini( 'GeneralSettings', 'SecuredTokenURL', 'janrain.ini' )
     $tokenURLProtocol = cond( $securedTokenURL|eq( 'enabled' ), 'http', 'https' )
     $currentLocaleCode = fetch( 'content', 'locale' ).locale_code
     $languageMap = ezini( 'LanguageSettings', 'LanguageMap', 'janrain.ini' )
     $languageCode = ezini( 'LanguageSettings', 'DefaultLanguage', 'janrain.ini' )
     $linkClass = ezini( 'ModalSettings', 'SigninLinkClass', 'janrain.ini' )
     $defaultProvider = ezini( 'GeneralSettings', 'DefaultProvider', 'janrain.ini' )
     $showProviderList = ezini( 'GeneralSettings', 'AlwaysShowProviderList', 'janrain.ini' )}
{if is_set( $languageMap[$currentLocaleCode] )}{set $languageCode = $languageMap[$currentLocaleCode]}{/if}
<script type="text/javascript">
  var rpxJsHost = (("https:" == document.location.protocol) ? "https://" : "http://static.");
  document.write(unescape("%3Cscript src='" + rpxJsHost +
"rpxnow.com/js/lib/rpx.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
  RPXNOW.overlay = true;
  RPXNOW.language_preference = '{$languageCode}';
{if $defaultProvider}
  
  RPXNOW.default_provider = '{$defaultProvider}';
{/if}
{if $showProviderList|eq( 'enabled' )}
  
  RPXNOW.flags = 'show_provider_list';
{/if}
  
  $(document).ready(function(){ldelim}
  
    $('a.{$linkClass}').click(function(){ldelim}
    
        return false;
    {rdelim})
  {rdelim});
</script>