<?php

class WRGoogleLogin {

	/**
	 * Create and return an instance of Google_Client
	 *
	 * @param string $id Client ID from Google Console
	 * @param string $secret Client Secret key from Google Console
	 * @param string $redirect_uri Redirect URI from Google Console
	 * @return Google_Client
	 */
	public static function createClient( $id, $secret, $redirect_uri )
	{
		$client = new Google_Client();
		$client->setClientSecret( $secret );
		$client->setClientId( $id );
		$client->setRedirectUri( $redirect_uri );

		return $client;
	}

	/**
	 * Get user data from a Google identity scope (listed at https://developers.google.com/identity/protocols/googlescopes )
	 * using a token posessed by Google_Client::fetchAccessTokenWithAuthCode
	 *
	 * @param string $OAuthApiEndpoint
	 * @param string $token
	 * @return array|bool
	 */
	public static function getUserDataWithToken( $OAuthApiEndpoint, $token ) {
		$data = [
			'alt' => 'json',
			'access_token' => $token
		];

		$data = http_build_query( $data );

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, 'GET' );
		curl_setopt( $curl, CURLOPT_URL, $OAuthApiEndpoint.'?'.$data );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

		$result = curl_exec( $curl );

		$userData = json_decode( $result, true );

		if ( array_key_exists( 'error', $userData ) ) {
			return false;
		}

		return $userData;
	}

	/**
	 * Login using user id
	 *
	 * @param int $id
	 * @return User|bool
	 */
	public static function loginUser( $id ) {
		$user = User::newFromId( $id );
		$user->setCookies();

		if ( $user->getId() === 0 ) {
			return false;
		}

		return $user;
	}

	/**
	 * Register the user and log them in
	 *
	 * @param string $name Display name
	 * @param string $password
	 * @param string $realName Real name
	 * @param string $email Email
	 * @return User|bool
	 */
	public static function registerUser( $name, $password, $realName = '', $email = '' ) {
		$user = User::newFromName( $name );

		if ( !$user ) return false;

		if ( $user->getId() === 0 ) {
			$user->addToDatabase();
			$user->setRealName( $realName );
			$user->setEmail( $email );
			$user->setPassword( $password );
			$user->setToken();
			$user->saveSettings();
		} else {
			return false;
		}

		$user->setOption( 'rememberpassword', 1 );

		return $user;
	}

	/**
	 * Register a Google user and link it to an existing MediaWiki User ID
	 *
	 * @param int $id User ID to link Google user to
	 * @param string $email User's Google email address
	 * @return bool
	 */
	public static function registerGoogleUser( $id, $email ) {
		$dbw = wfGetDB( DB_MASTER );

		$result = $dbw->insert( 'user_google_user', [
			'mw_user_id' => $id,
			'user_email' => $email
		] );

		return (bool)$result;
	}

	/**
	 * Check for existing Google users with a given email address
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function isExistingGoogleUser( $email ) {
		$dbr = wfGetDB( DB_REPLICA );

		$result = $dbr->select( 'user_google_user',
			[ 'mw_user_id' ],
			[ 'user_email' => $email ]
		);

		$result = $result->fetchObject();

		return (bool)$result ? $result->mw_user_id : false;
	}

	/**
	 * Check for existing user with a given email address
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function isExistingMediaWikiUser( $email ) {
		$dbr = wfGetDB( DB_REPLICA );

		$result = $dbr->select( 'user',
			[ 'user_id' ],
			[ 'user_email' => $email ]
		);

		$result = $result->fetchObject();

		return (bool)$result ? $result->user_id : false;
	}

}