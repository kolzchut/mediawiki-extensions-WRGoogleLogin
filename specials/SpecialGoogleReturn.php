<?php

/**
 * Redirect SpecialPage
 */
class SpecialGoogleReturn extends SpecialPage {

	public function __construct() {
		parent::__construct( 'GoogleReturn' );
	}

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 */
	public function execute( $sub ) {
		global $wgWRGoogleLoginConfig;

		$out      = $this->getOutput();
		$request  = $this->getRequest();
		$params   = $request->getQueryValues();
		$returnto = $params[ 'state' ];

		// If no $returnto value was set, set it to the main page
		if ( !isset( $returnto ) ) {
			$returnto = $out->redirect( Title::newMainPage()->getFullUrl() );
		}

		// Show error if user returns from Google with an error
		if ( array_key_exists( 'error', $params ) ) {
			$out->showFatalError( wfMessage( 'wr-google-login-return-page-google-error' ) );
			return;
		}

		// Create Google_Client instance
		$client = WRGoogleLogin::createClient(
			$wgWRGoogleLoginConfig[ 'client_id' ],
			$wgWRGoogleLoginConfig[ 'client_secret' ],
			$wgWRGoogleLoginConfig[ 'redirect_uri' ]
		);

		$authCode  = $params[ 'code' ];
		$tokenData = $client->fetchAccessTokenWithAuthCode( $authCode );

		// Show error if auth code is invalid
		if ( array_key_exists( 'error', $tokenData ) ) {
			$out->showFatalError( wfMessage( 'wr-google-login-return-page-token-error' ) );
			return;
		}

		// Get user profile data with token
		$token = $tokenData[ 'access_token' ];
		$userData = WRGoogleLogin::getUserDataWithToken( 'https://www.googleapis.com/oauth2/v1/userinfo', $token );

		// Show error if data fetching failed
		if ( !$userData ) {
			$out->showFatalError( wfMessage( 'wr-google-login-return-page-data-failed' ) );
			return;
		}

		// Store user details
		$email      = $userData[ 'email' ];
		$realName   = $userData[ 'name' ];
		$googleUser = WRGoogleLogin::isExistingGoogleUser( $email );

		// If user is a Google user, log them in
		if ( $googleUser ) {
			WRGoogleLogin::loginUser( $googleUser );

			$this->redirect( $returnto );
		}

		$mwUser = WRGoogleLogin::isExistingMediaWikiUser( $email );

		// If user is registered in MediaWiki, create new Google user record, link them together and login
		if ( $mwUser && !$googleUser ) {
			WRGoogleLogin::registerGoogleUser( $mwUser, $email );
			WRGoogleLogin::loginUser( $mwUser );

			$this->redirect( $returnto );
		}

		// If user doesn't exist, register them and log them in
		if ( !$mwUser && !$googleUser ) {
			$username = UIDGenerator::newUUIDv4();
			$newUser = WRGoogleLogin::registerUser( $username, User::randomPassword(), $realName, $email );

			// If new user creation fails, show an error
			if ( !$newUser ) {
				$out->showFatalError( wfMessage( 'wr-google-login-return-page-new-user-error' ) );

				return;
			}

			$newUserId = $newUser->getId();

			// Add a Google record linked to the user and log them in
			$user = WRGoogleLogin::registerGoogleUser( $newUserId, $email );
			WRGoogleLogin::loginUser( $newUserId );

			$this->redirect( $returnto );
		}
	}

	private function redirect( $url ) {
		global $wgOut;

		$wgOut->redirect( Skin::makeInternalOrExternalUrl( $url ) );
	}

}