<?php
/**
 * @package  Bitbucket
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  1.2.0
 */

namespace Plugin\Bitbucket;

class Base extends \Plugin {

	/**
	 * Initialize the plugin
	 */
	public function _load() {
		$f3 = \Base::instance();
		$f3->config(__DIR__ . DIRECTORY_SEPARATOR . "config.ini");
		$f3->route("POST|GET /bitbucket-post", "Plugin\Bitbucket\Controller->post");
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
		fwrite($fh, "[globals]\nsite.plugins.bitbucket.token=$token\n");
		fwrite($fh, "[globals]\nsite.plugins.bitbucket.default_user_id=1\n");
		fclose($fh);
	}

	/**
	 * Check if plugin is installed
	 * @return bool
	 */
	public function _installed() {
		return is_file(__DIR__ . DIRECTORY_SEPARATOR . "config.ini");
	}

	/**
	 * Generate page for admin panel
	 */
	public function _admin() {
		$f3 = \Base::instance();
		$f3->set("UI", $f3->get("UI") . ";./app/plugin/bitbucket/");
		echo \Helper\View::instance()->render("view/admin.html");
	}

}
