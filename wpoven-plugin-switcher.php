<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.wpoven.com/plugins/
 * @since             1.0.0
 * @package           Wpoven_Plugin_Switcher
 *
 * @wordpress-plugin
 * Plugin Name:       WPOven Plugin Switcher
 * Plugin URI:        https://www.wpoven.com/plugins/wpoven-plugin-switcher
 * Description:       Allows users to quickly enable or disable plugins as per need. 
 * Version:           1.0.0
 * Author:            WPOven
 * Author URI:        https://www.wpoven.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpoven-plugin-switcher
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WPOVEN_PLUGIN_SWITCHER_VERSION', '1.0.0');
if (!defined('WPOVEN_PLUGIN_SWITCHER_SLUG'))
	define('WPOVEN_PLUGIN_SWITCHER_SLUG', 'wpoven-plugin-switcher');

define('WPOVEN_PLUGIN_SWITCHER', 'WPOven Plugin Switcher');
define('WPOVEN_PLUGIN_SWITCHER_ROOT_PL', __FILE__);
define('WPOVEN_PLUGIN_SWITCHER_ROOT_URL', plugins_url('', WPOVEN_PLUGIN_SWITCHER_ROOT_PL));
define('WPOVEN_PLUGIN_SWITCHER_ROOT_DIR', dirname(WPOVEN_PLUGIN_SWITCHER_ROOT_PL));
define('WPOVEN_PLUGIN_SWITCHER_PLUGIN_DIR', plugin_dir_path(__DIR__));
define('WPOVEN_PLUGIN_SWITCHER_PLUGIN_BASE', plugin_basename(WPOVEN_PLUGIN_SWITCHER_ROOT_PL));
define('WPOVEN_SWITCHER_PATH', realpath(plugin_dir_path(WPOVEN_PLUGIN_SWITCHER_ROOT_PL)) . '/');

include_once WPOVEN_SWITCHER_PATH  . 'source/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/baseapp/wpoven_pluginswitcher',
	__FILE__,
	'wpoven-plugin-switcher'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpoven-plugin-switcher-activator.php
 */
function activate_wpoven_plugin_switcher()
{
	require_once plugin_dir_path(__FILE__) . 'source/includes/class-wpoven-plugin-switcher-activator.php';
	Wpoven_Plugin_Switcher_Activator::activate();

	if (empty(get_option('wpoven-plugin-switcher-status')) || get_option('wpoven-plugin-switcher-status')) {
		update_option('wpoven-plugin-switcher-status', 'active');
	} else {
		add_option('wpoven-plugin-switcher-status', 'active');
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpoven-plugin-switcher-deactivator.php
 */
function deactivate_wpoven_plugin_switcher()
{
	require_once plugin_dir_path(__FILE__) . 'source/includes/class-wpoven-plugin-switcher-deactivator.php';
	Wpoven_Plugin_Switcher_Deactivator::deactivate();

	if (empty(get_option('wpoven-plugin-switcher-status')) || get_option('wpoven-plugin-switcher-status')) {
		update_option('wpoven-plugin-switcher-status', 'deactive');
	} else {
		add_option('wpoven-plugin-switcher-status', 'deactive');
	}

	$mu_plugins_dir_path = WP_CONTENT_DIR . '/' . 'mu-plugins';
	$mu_plugin_file_path = $mu_plugins_dir_path . '/class-wpoven-plugin-switcher-mu-plugin.php';
	if (file_exists($mu_plugin_file_path)) {
		wp_delete_file($mu_plugin_file_path);
	}
}

register_activation_hook(__FILE__, 'activate_wpoven_plugin_switcher');
register_deactivation_hook(__FILE__, 'deactivate_wpoven_plugin_switcher');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'source/includes/class-wpoven-plugin-switcher.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpoven_plugin_switcher()
{
	$plugin = new Wpoven_Plugin_Switcher();
	$plugin->run();
}
run_wpoven_plugin_switcher();

function wpoven_plugin_switcher_plugin_settings_link($links)
{

	$settings_link = '<a href="' . admin_url('admin.php?page=' . WPOVEN_PLUGIN_SWITCHER_SLUG) . '">Settings</a>';

	array_push($links, $settings_link);
	return $links;
}

add_filter('plugin_action_links_' . WPOVEN_PLUGIN_SWITCHER_PLUGIN_BASE, 'wpoven_plugin_switcher_plugin_settings_link');
