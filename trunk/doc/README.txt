============================
 Janrain Auth Documentation
============================

*Version 1.0 - 2010 Jerome Vieilledent*

:Date: 2010/08/26

.. contents:: Table of contents

--------------
 PRESENTATION
--------------
Janrain Auth allows you to let your website users to log into your eZ Publish powered application
by signing in their preferred social network such as Facebook, Twitter and Google.

This extension is an implementation of `Janrain Engage <http://www.janrain.com>`_ (formerly RPX) authentication framework.
It has a dependency on `SQLI Import <http://projects.ez.no/sqliimport`_  for its content API, so you'll need to 
install it before using Janrain Auth


---------
 LICENCE
---------
This eZ Publish extension is provided *as is*, in GPL v2 (see LICENCE).


-------
 SETUP
-------
In order to use this extension, you will first need to create an account and a 
`Janrain Engage <http://www.janrain.com>`_ *application*.

Once your *Janrain Engage* application set up, get your **API key**, **Application ID** and **Application Domain** to insert
them in an override of **janrain.ini** config file.

Check comments in **janrain.ini** file for more info about customization.

User related information
========================
Janrain Auth will create a valid eZ Publish user once the internet user has been authenticated on the chosen social network.

The content class to use for user creation is managed by **site.ini** [UserSettings].UserClassID setting.
You can also map user information returned by Janrain to user content object attribute. You can configure this map
in an override of **janrain.ini**, [UserSettings].AuthInfoMap


-------
 USAGE
------- 
This extension is usable out-of-the-box with ezwebin/ezflow. It provides **login.tpl** and **page_footer_script.tpl** template overrides.
In order to be able to trigger the modal login window, you will need to setup a link with a **rpxnow** class and the right Href :

::

  {def $signinURL = fetch( 'janrain', 'signin_url' )}
  <a class="rpxnow" href="{$signinURL}"> Sign In </a>

You will also need to include modal script template at the bottom of your page (before </body>)
(already included for ezwebin/ezflow powered websites) :

::

  {include uri='design:janrain/modalscript.tpl'}


