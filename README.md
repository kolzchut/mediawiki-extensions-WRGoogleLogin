# WRGoogleLogin

This extension handles registration of users, using Google's OAuth authentication mechanism.

## Configuration

WRGoogleLoginConfig -
- `client_id` - Client ID, obtained from Google Console
- `client_secret` - Client secret key, obtained from Google Console
- `redirect_uri` - The URL to redirect the user when they authenticate via Google; should also appear on the Google Console Redirect URIs list

## How to use

When wanting to register or sign a user in, simply make them open the `Special:GoogleSignin` page. If they're registered, it will perform the OAuth process and sign them in. If not, they'll be registered using their email and get signed in automatically and seamlessly.

### Redirection

If you want to redirect the user to a specific page after they return from Google, add a `returnto` query parameter to the Special:Signin page, e.g. `https://www.example-wiki.com/Special:GoogleSignin?returnto=Some_title`.
We use `Skin::makeInternalOrExternalUrl()` to redirect the user to the given `returnto` value, so you can set a relative path as well as an absolute one.