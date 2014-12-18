<?php
/**
 * @package  Bitbucket
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  1.0.0
 */

namespace Plugin\Bitbucket;

class Base extends \Plugin {

	/**
	 * Initialize the plugin
	 */
	public function _load() {
		$f3 = \Base::instance();
		$f3->route("POST /bitbucket-post", "Plugin\Bitbucket\Controller->post");
	}

	/**
	 * Install plugin (generate configuration file)
	 */
	public function _install() {
		$fh = fopen(__DIR__ . DIRECTORY_SEPARATOR . "config.ini", "w");
		if($fh === false) {
			throw new Exception("Unable to write to Bitbucket plugin configuration file");
		}

		$token = \Helper\Security::instance()->salt();
		$f3 = \Base::instance();
		$f3->set("success", "Bitbucket plugin installed. Hook URL: " . $f3->get("site.url") . "bitbucket-post?token=$token");
		$f3->set("site.plugins.bitbucket.token", $token);
		fwrite($fh, "[globals]\nsite.plugins.bitbucket.token=$token\n");
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
