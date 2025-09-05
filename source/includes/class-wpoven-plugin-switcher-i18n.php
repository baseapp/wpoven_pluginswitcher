<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.wpoven.com/plugins/
 * @since      1.0.0
 *
 * @package    Wpoven_Plugin_Switcher
 * @subpackage Wpoven_Plugin_Switcher/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wpoven_Plugin_Switcher
 * @subpackage Wpoven_Plugin_Switcher/includes
 * @author     WPOven <contact@wpoven.com>
 */
class Wpoven_Plugin_Switcher_i18n
{


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain()
	{

		// Since WP 4.6, WordPress.org plugins don’t need load_plugin_textdomain().
		// This fallback ensures translations still load if distributed outside wp.org.
		$domain = 'wpoven-plugin-switcher';

		// First, try to load from WordPress.org language packs (auto-handled).
		if (! is_textdomain_loaded($domain)) {
			// Fallback: load from /languages directory inside the plugin.
			load_plugin_textdomain(
				$domain,
				false,
				dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
			);
		}
	}
}
