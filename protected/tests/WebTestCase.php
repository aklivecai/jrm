<?php
define('TEST_BASE_URL','http://localhost/GitHub/CRM/index-test.php/');

class WebTestCase extends CWebTestCae
{
	protected function setUp(){
		parent::setUp();
		$this->setBrowserUrl(TEST_BASE_URL);
	}
}

