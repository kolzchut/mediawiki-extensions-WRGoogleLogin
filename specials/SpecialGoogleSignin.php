<?php

/**
 * Redirect SpecialPage
 */
class SpecialGoogleSignin extends SpecialPage {
	public function __construct() {
		parent::__construct( 'GoogleSignin' );
	}

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 */
	public function execute( $sub ) {
		global $wgWRGoogleLoginConfig;

		$client = WRGoogleLogin::createClient(
			$wgWRGoogleLoginConfig[ 'client_id' ],
			$wgWRGoogleLoginConfig[ 'client_secret' ],
			$wgWRGoogleLoginConfig[ 'redirect_uri' ]
		);

		$client->addScope( 'https://www.googleapis.com/auth/userinfo.profile' );
		$client->addScope( 'https://www.googleapis.com/auth/userinfo.email' );
		$auth_url = $client->createAuthUrl();

		header( 'Location: ' . Skin::makeInternalOrExternalUrl( $auth_url ) );

	}

}
