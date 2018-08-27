WRGoogleLogin
=============

This extension handles registration of users, using Google's OAuth authentication mechanism.

## Preliminary steps
To use this extension, you need to set up credentials in Google's
Cloud Platform Console. Instructions are available here:
https://support.google.com/cloud/answer/6158849?hl=en
When setting up OAuth, you need to add  "Authorized redirect URIs".
These are the the URLs for Special:GoogleReturn, for example:
   - https://example.com/wiki/Special:GoogleReturn
   - https://example.com/wiki/Spécial:GoogleReturn (for a French wiki)

## Installation
- Checkout the extension's code, placing it under $IP/extensions/WikiRights/WRGoogleLogin
- Add `wfLoadExtension('WikiRights/WRGoogleLogin');` to `LocalSettings.php`
- add the extension's `composer.json` to MediaWiki's `composer.local.json`
  (see https://www.mediawiki.org/wiki/Composer#Using_composer-merge-plugin)
- Run `composer update` to install the extension's dependencies
- Run `php maintenance/update.php` to create the required database table.

## Configuration
All configuration is done through `$wgWRGoogleLoginConfig`, which is an
array of the following options:
- `client_id`:      Client ID, obtained from Google Console
- `client_secret`:  Client secret key, obtained from Google Console
- `redirect_uri`:   The URL to redirect the user when they authenticate
                    via Google; should also appear on the Google Console
                    Redirect URIs list. This defaults to `Special:GoogleReturn`.

## How to use
When wanting to register or sign a user in, simply make them open the
`Special:GoogleSignin` page. If they're registered, it will perform the
OAuth process and sign them in. If not, they'll be registered using
their email and get signed in automatically and seamlessly.

### Redirection
If you want to redirect the user to a specific page after they return
from Google, add a `returnto` query parameter to the Special:Signin
page, e.g. `https://www.example.com/wiki/Special:GoogleSignin?returnto=Some_title`.
We use `Skin::makeInternalOrExternalUrl()` to redirect the user to the
given `returnto` value, so you can set a relative path as well as an
absolute one.
