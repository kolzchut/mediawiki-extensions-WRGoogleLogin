# WRGoogleLogin

This extension handles registration of users, using Google's OAuth authentication mechanism.

## Configuration

WRGoogleLoginConfig -
- `client_id` - Client ID, obtained from Google Console
- `client_secret` - Client secret key, obtained from Google Console
- `redirect_uri` - The URL to redirect the user when they authenticate via Google; should also appear on the Google Console Redirect URIs list

## How to use

When wanting to register or sign a user in, simply make them open the `Special:GoogleSignin` page. If they're registered, it will perform the OAuth process and sign them in. If not, they'll be registered using their email and get signed in automatically and seamlessly.