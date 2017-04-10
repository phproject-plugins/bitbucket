<?php
/**
 * @package  Bitbucket
 * @author   Alan Hardman <alan@phpizza.com>
 * @version  2.0.0
 */

namespace Plugin\Bitbucket;

class Base extends \Plugin {

	const CONFIG_KEY_VERSION = "site.plugins.bitbucket.version";
	const CONFIG_KEY_TOKEN = "site.plugins.bitbucket.token";
	const CONFIG_KEY_DEFAULT_USER = "site.plugins.bitbucket.default_user_id";

	/**
	 * Initialize the plugin
	 */
	public function _load() {
		$f3 = \Base::instance();

		// Convert INI configuration to DB configuration
		if(is_file(__DIR__ . "/config.ini")) {
			$f3->config(__DIR__ . "/config.ini");
			\Model\Config::setVal(self::CONFIG_KEY_TOKEN, \Base::instance()->get(self::CONFIG_KEY_TOKEN));
			unlink(__DIR__ . "/config.ini");
		}

		$f3->route("POST|GET /bitbucket-post", "Plugin\Bitbucket\Controller->post");
	}

	/**
	 * Install plugin, adding default configuration and database table
	 */
	public function _install() {
		$f3 = \Base::instance();
		$db = $f3->get("db.instance");
		$install_db = file_get_contents(__DIR__ . "/db.sql");
		$db->exec(explode(";", $install_db));

		// Add default configuration if not upgrading from 1.x
		if (!$f3->exists(self::CONFIG_KEY_TOKEN)) {
			$token = \Helper\Security::instance()->salt();
			\Model\Config::setVal(self::CONFIG_KEY_TOKEN, $token);
			\Model\Config::setVal(self::CONFIG_KEY_DEFAULT_USER, 1);
			$f3->set("success", "Bitbucket plugin installed. Hook URL: " .
				$f3->get("site.url") . "bitbucket-post?token=$token");
		}

		\Model\Config::setVal(self::CONFIG_KEY_VERSION, '2.0.0');
	}

	/**
	 * Check if plugin is installed
	 * @return bool
	 */
	public function _installed() {
		$f3 = \Base::instance();
		return $f3->exists(self::CONFIG_KEY_TOKEN) &&
			version_compare($f3->get(self::CONFIG_KEY_VERSION), '2', '>=');
	}

	/**
	 * Generate page for admin panel
	 */
	public function _admin() {
		echo \Helper\View::instance()->render("bitbucket/view/admin.html");
	}

}
