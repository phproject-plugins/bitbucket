<?php

class Bitbucket extends \Plugin {

	/**
	 * Handle HTTP POST request
	 * @param Base  $f3
	 * @param array $params
	 */
	public function post($f3, $params) {
		$post = json_decode(file_get_contents('php://input'), true);
	}

	/**
	 * Install plugin (generate configuration file)
	 */
	public function _install() {
		$fh = fopen(__DIR__ . DIRECTORY_SEPARATOR . "config.ini", "w");
		if($fh === false) {
			throw new Exception("Unable to write to Bitbucket plugin configuration file");
		}

		$token = \Helper\Security::instance()->hash();
		$f3 = \Base::instance();
		$f3->set("success", "Bitbucket plugin installed. Hook URL: " . $f3->get("site.url") . "bitbucket-post?token=$token");
		$f3->set("site.plugins.bitbucket.token", $token);
		fwrite($fh, "[globals]\nsite.plugins.bitbucket.token=$token");
		fclose($fh);
	}

	/**
	 * Check if plugin is installed
	 * @return bool
	 */
	public function _installed() {
		return is_file(__DIR__ . DIRECTORY_SEPARATOR . "config.ini");
	}


}
