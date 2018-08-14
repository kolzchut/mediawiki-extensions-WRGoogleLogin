<?php

/**
 * HelloWorld SpecialPage
 */
class SpecialHelloWorld extends SpecialPage {
	public function __construct() {
		parent::__construct( 'HelloWorld' );
	}

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 *  [[Special:HelloWorld/subpage]].
	 */
	public function execute( $sub ) {
		$out = $this->getOutput();

		$out->setPageTitle( 'Hello' );

		$user = User::newFromId( 1 );

		echo '<pre style="direction: ltr; text-align: left;">';
		print_r($a);
		echo '</pre>';

	}

}
