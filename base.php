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
		if(is_file(__DIR__ . "/config.ini")) {
			$f3->config(__DIR__ . "/config.ini");
		}
		$f3->route("POST|GET /bitbucket-post", "Plugin\Bitbucket\Controller->post");
	}

	/**
	 * Install plugin (generate configuration file)
	 */
	public function _install() {
		$token = \Helper\Security::instance()->salt();
		$f3 = \Base::instance();
		$f3->set("success", "Bitbucket plugin installed. Hook URL: " . $f3->get("site.url") . "bitbucket-post?token=$token");
		\Model\Config::setVal("site.plugins.bitbucket.token", $token);
		\Model\Config::setVal("site.plugins.bitbucket.default_user_id", 1);
	}

	/**
	 * Check if plugin is installed
	 * @return bool
	 */
	public function _installed() {
		return \Base::instance()->get("site.plugins.bitbucket.token") ||
			is_file(__DIR__ . "/config.ini");
	}

	/**
	 * Generate page for admin panel
	 */
	public function _admin() {
		echo \Helper\View::instance()->render("bitbucket/view/admin.html");
	}

}
