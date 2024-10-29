<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.wpoven.com/plugins/
 * @since      1.0.0
 *
 * @package    Wpoven_Plugin_Switcher
 * @subpackage Wpoven_Plugin_Switcher/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpoven_Plugin_Switcher
 * @subpackage Wpoven_Plugin_Switcher/admin
 * @author     WPOven <contact@wpoven.com>
 */
class Wpoven_Plugin_Switcher_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $_wpoven_plugin_switcher;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if (!class_exists('ReduxFramework') && file_exists(require_once plugin_dir_path(dirname(__FILE__)) . 'redux-framework/redux-core/framework.php')) {
			require_once plugin_dir_path(dirname(__FILE__)) . 'redux-framework/redux-core/framework.php';
		}
		if (! function_exists('WP_Filesystem')) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}

		if (!function_exists('is_plugin_active')) {
			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpoven_Plugin_Switcher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpoven_Plugin_Switcher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wpoven-plugin-switcher-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wpoven_Plugin_Switcher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wpoven_Plugin_Switcher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wpoven-plugin-switcher-admin.js', array('jquery'), $this->version, false);
	}

	function getRandomString($n)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';

		for ($i = 0; $i < $n; $i++) {
			$index = random_int(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}

		return $randomString;
	}

	function wpoven_plugin_switcher_optimized_group_of_plugins($options)
	{
		$ruleStatus = get_option('wpoven-plugin-switcher-rules-setting');
		if (!is_array($options) || empty($ruleStatus['wps_settings']['wps_rules'])) {
			$options = array();
			$options['wps_settings']['wps_rules']['v' . $this->getRandomString(4) . '_name'] =  'Custom Rule';
			update_option('wpoven-plugin-switcher-rules-setting', $options);
		};


		$create = isset($_POST['rule_name']) ? sanitize_text_field(wp_unslash($_POST['rule_name'])) : false;

		if ($create) {
			$ruleName = sanitize_text_field(wp_unslash($_POST['rule_name']));
			$uid = $this->getRandomString(4);

			if (!isset($options['wps_settings']) || !is_array($options['wps_settings'])) {
				$options['wps_settings'] = array();
			}

			if (!isset($options['wps_settings']['wps_rules'])) {
				$options['wps_settings']['wps_rules'] = array();
			}

			$options['wps_settings']['wps_rules']['v' . $uid . '_name'] =  $ruleName;
			update_option('wpoven-plugin-switcher-rules-setting', $options);
		}

		$delete = isset($_GET['delete']) ? sanitize_text_field(wp_unslash($_GET['delete'])) : false;

		if ($delete) {
			$uid = sanitize_text_field($delete);
			foreach ($options['wps_settings']['wps_rules'] as $key => $rule) {
				if (isset($options['wps_settings']['wps_rules'][$key]) && strstr($key, $uid)) {
					unset($options['wps_settings']['wps_rules'][$key]);
					update_option(WPOVEN_PLUGIN_SWITCHER_SLUG . '-rules-setting', $options);

					$this->wpoven_plugin_options_process(get_option(WPOVEN_PLUGIN_SWITCHER_SLUG));

					header('Location: ' . admin_url('/admin.php?page=wpoven-plugin-switcher'));
					die();
				}
			}
		}

		$active_plugins = get_option('active_plugins');
		$plugin_options = array();

		foreach ($active_plugins as $key => $plugin) {
			$parts = explode("/", $plugin);
			$option_value = $parts[0];
			$plugin_options[$plugin] = $option_value;
		}

		$accordions = array();
		$addRule = array(
			'id'         => 'add-rule-button',
			'class'      => 'add-rule-button',
			'type'       => 'button_set',
			'title'      => '&nbsp;',
			'options'    => array(
				'enabled'  => 'Add Rule',
			),
			'desc'       => 'Create new rules to activate or deactivate plugins selectively based on pages, posts, or specific URLs.',
			'default'    => 'enabled'
		);
		$accordions[] = $addRule;

		if (isset($options['wps_settings']['wps_rules'])) {

			$uids = array();

			foreach ($options['wps_settings']['wps_rules'] as $var => $val) {
				if (strstr($var, '_name')) {
					list($uid, $dummy) = explode('_', $var);
					$uids[] = $uid;
				}
			}

			foreach ($uids as $uid) {
				$rules = $options['wps_settings']['wps_rules'];
				$ruleName   = isset($rules[$uid . '_name']) ? $rules[$uid . '_name'] : '';

				$current_url = admin_url('admin.php');
				// Check if 'QUERY_STRING' is set and not empty
				if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
					$query_string = sanitize_text_field(wp_unslash($_SERVER['QUERY_STRING']));
					$current_url = add_query_arg($query_string, '', $current_url);
				}

				$delteURL = $current_url . '&delete=' . $uid;

				$ruleStatus = get_option('wpoven-plugin-switcher');
				$accordion_class = '';
				if (isset($ruleStatus[$uid . '_status'])) {
					if ($ruleStatus[$uid . '_status'] == true) {
						$accordion_class = 'accordion_bg_color';
					}
				}
				$open = false;
				if (isset($_GET['rule']) && $_GET['rule'] == $ruleName) {
					$open = true;
				}

				$accordionStart = array(
					'id'        => $uid . '-start',
					'type'      => 'accordion',
					'title'     => $ruleName,
					'subtitle'     => admin_url('admin.php?page=' . WPOVEN_PLUGIN_SWITCHER_SLUG . '&rule=' . $ruleName),
					'class'     => $accordion_class,
					'open'      => $open,
					'position'  => 'start',
				);

				$nameField = array(
					'id'      => $uid . '_name',
					'type'    => 'text',
					'title'   => 'Name',
					'class'   => 'wps-rule-name',
					'desc'    => '<a href="' . $delteURL . '" style="cursor: pointer;" onclick="return confirm(\'Are you sure you want to delete this rule ?\');" >Delete Rule</a>',
					'default' =>  $ruleName,
				);

				$statusField = array(
					'id'    => $uid . '_status',
					'type'  => 'switch',
					'title' => 'Status',
					'desc'  => 'Check the status to see if the rule is currently active (on) or inactive (off) and working as intended.'
				);

				$typeField = array(
					'id'       => $uid . '_type',
					'type'     => 'select',
					'title'    => 'Rule Type',
					'options'  => array(
						'page' => 'Page',
						'post' => 'Post',
						'url'  => 'URL',
						'home_page'  => 'Home Page',
					),
					'desc'     => 'Select rule type: page, post, home page or URL for plugin activation or deactivation."',
					'default'  => 'page',
				);

				$pageField = array(
					'id'          => $uid . "_pages",
					'type'        => 'select',
					'title'       => 'Select Pages',
					'placeholder' => 'Select an option',
					'required'    => array($uid . '_type', 'equals', 'page'),
					'desc'        => 'Choose pages to activate or deactivate. Multi-selection supported.',
					'multi'       => true,
					//'ajax     ' => true,
					'data'        => 'pages',
				);

				$postField = array(
					'id'          => $uid . "_posts",
					'type'        => 'select',
					'title'       => 'Select Posts',
					'required'    => array($uid . '_type', 'equals', 'post'),
					'placeholder' => 'Select an option',
					'desc'        => 'Choose posts to activate or deactivate. Multi-selection supported.',
					'multi'       => true,
					//'ajax'      => true,
					'data'        => 'posts'
				);

				$urlField = array(
					'id'          => $uid . "_url",
					'type'        => 'text',
					'title'       => 'URL Match',
					'required' => array($uid . '_type', 'equals', 'url'),
					'placeholder' => 'E.g: /contact-us',
					'desc'        => 'Enter the URL slug to match for activation or deactivation. Example: /contact-us.',
				);

				$postMatch = array(
					'id'      => $uid . '_post_match',
					'type'    => 'text',
					'title'   => 'Post Match',
					'required' => array($uid . '_type', 'equals', 'url'),
					'desc'    => 'Post Match for a URL.'
				);

				$pluginField = array(
					'id'          => $uid . "_plugins",
					'type'        => 'select',
					'title'       => 'Select Plugins',
					'placeholder' => 'Select an option',
					'chosen'      => true,
					'multi'       => true,
					//'validate' => 'not_empty',
					'desc'        => 'Select plugins to activate or deactivate. No effect on pages if none are selected. Multi-selection supported.',
					//'ajax'      => true,
					'data'        => $plugin_options,
				);

				$pluginStatusField = array(
					'id'    => $uid . '_plugin_status',
					'type'  => 'switch',
					'title' => 'Plugin Status',
					'on'    => 'Activate',
					'off'   => 'Deactivate',
					'desc'  => 'This rule should activate the plugin or deactivate active plugins based on your selection.',
					'text_width' => 100,
				);

				$accordionEnd = array(
					'id'        => $this->getRandomString(4) . '-end',
					'type'      => 'accordion',
					'position'  => 'end'
				);

				$accordions[] = $accordionStart;
				$accordions[] = $nameField;
				$accordions[] = $statusField;
				$accordions[] = $typeField;
				$accordions[] = $pageField;
				$accordions[] = $postField;
				$accordions[] = $urlField;
				$accordions[] = $postMatch;
				$accordions[] = $pluginField;
				$accordions[] = $pluginStatusField;
				$accordions[] = $accordionEnd;
			}
		}
		$result = $accordions;

		return $result;
	}

	/**
	 * Set wpoven plugin switcher admin page.
	 */
	function setup_gui()
	{
		$options = get_option(WPOVEN_PLUGIN_SWITCHER_SLUG . '-rules-setting');

		if (!class_exists('Redux')) {
			return;
		}

		$opt_name = WPOVEN_PLUGIN_SWITCHER_SLUG;
		Redux::disable_demo();

		/**
		 * All the possible arguments for Redux.
		 * For full documentation on arguments, please refer to: https://devs.redux.io/core/arguments/
		 */

		$args = array(
			'opt_name'                  => $opt_name,
			'display_name'              => 'WPOven Plugin Switcher',
			'display_version'           => ' ',
			//'menu_type'                 => 'menu',
			'allow_sub_menu'            => true,
			//'menu_title'                => esc_html__('WPOven Plugins', 'your-textdomain-here'),
			//'page_title'                => esc_html__('Plugin Switcher', 'your-textdomain-here'),
			'disable_google_fonts_link' => false,
			'admin_bar'                 => false,
			'admin_bar_icon'            => 'dashicons-portfolio',
			'admin_bar_priority'        => 50,
			'global_variable'           => $opt_name,
			'dev_mode'                  => false,
			'customizer'                => false,
			'open_expanded'             => false,
			'disable_save_warn'         => false,
			'page_priority'             => 90,
			'page_parent'               => 'themes.php',
			'page_permissions'          => 'manage_options',
			'menu_icon'                 => plugin_dir_url(__FILE__) . '/img/logo.png',
			'last_tab'                  => '',
			'page_icon'                 => 'icon-themes',
			'page_slug'                 => $opt_name,
			'save_defaults'             => false,
			'default_show'              => false,
			'default_mark'              => '',
			'show_import_export'        => false,
			'transient_time'            => 60 * MINUTE_IN_SECONDS,
			'output'                    => false,
			'output_tag'                => false,
			'footer_credit'             => 'Please rate WPOven Plugin Switcher ★★★★★ on WordPress.org to support us. Thank you!',
			'use_cdn'                   => false,
			'admin_theme'               => 'wp',
			'flyout_submenus'           => false,
			'font_display'              => 'swap',
			'hide_reset'                => true,
			'database'                  => '',
			'network_admin'           => '',
			'search'                    => false,
			'hide_expand'            => true,
		);

		Redux::set_args($opt_name, $args);

		/*
		* ---> START SECTIONS
		*/
		Redux::set_section(
			$opt_name,
			array(
				'title'            => esc_html__('Plugin Switcher', 'your-textdomain-here'),
				'id'               => 'wps_settings',
				'customizer_width' => '400px',
				'icon'             => 'dashicons dashicons-admin-plugins',
				'heading'		   => 'Activation & Deactivation Rules',
				'desc'             => 'Manage plugin activation and deactivation based on pages, posts, and custom URIs with flexible rules and conditions.',
				'fields' => $this->wpoven_plugin_switcher_optimized_group_of_plugins($options),
			)
		);
	}

	/**
	 *  Adding MU-Plugin file.
	 */
	function add_mu_plugin($enable)
	{
		global $wp_filesystem;
		WP_Filesystem();

		// Define paths
		$wpoven_muplugin_source_path = plugin_dir_path(dirname(__FILE__)) . 'mu-plugin/class-wpoven-plugin-switcher-mu-plugin.php';
		$mu_plugins_dir_path = WP_CONTENT_DIR . '/mu-plugins';
		$mu_plugin_file_path = $mu_plugins_dir_path . '/class-wpoven-plugin-switcher-mu-plugin.php';

		// Check if the mu-plugins directory exists; if not, create it
		if (! $wp_filesystem->is_dir($mu_plugins_dir_path)) {
			$wp_filesystem->mkdir($mu_plugins_dir_path, FS_CHMOD_DIR);
		}

		if ($enable) {
			// Copy the file if the directory exists and `$enable` is true
			if ($wp_filesystem->is_dir($mu_plugins_dir_path)) {
				$wp_filesystem->copy($wpoven_muplugin_source_path, $mu_plugin_file_path, true, FS_CHMOD_FILE);
			}
		} else {
			// Remove the file if `$enable` is false and the file exists
			if ($wp_filesystem->exists($mu_plugin_file_path)) {
				$wp_filesystem->delete($mu_plugin_file_path);
			}
		}
	}

	function wpoven_plugin_options_process($options)
	{
		$rules_setting = get_option(WPOVEN_PLUGIN_SWITCHER_SLUG . '-rules-setting');
		if (isset($rules_setting['wps_settings']['wps_rules']) && is_array($rules_setting['wps_settings']['wps_rules'])) {
			$rules = $options;

			$values = array('name', 'status', 'pages', 'posts', 'plugin_status', 'plugins', 'type', 'url', 'post_match'); // custom removed
			$uids = array();

			foreach ($rules_setting['wps_settings']['wps_rules'] as $var => $val) {
				if (strstr($var, '_name')) {
					list($uid, $dummy) = explode('_', $var);
					$uids[] = $uid;
				}
			}

			$rule_status = array();
			$processed_rules = array();
			foreach ($uids as $uid) {
				$processed_rule = array();
				foreach ($values as $value) {
					if (isset($rules[$uid . '_' . $value])) {
						$processed_rule[$value] = $rules[$uid . '_' . $value];
						if ($value == 'status') {
							$rule_status[] = $rules[$uid . '_status'];
						}
					}
				}
				$processed_rules[] = $processed_rule;
			}

			$enable = false;
			if (in_array(1, $rule_status)) {
				$enable = true;
			}
			$this->add_mu_plugin($enable);

			update_option(WPOVEN_PLUGIN_SWITCHER_SLUG . '-rules', $processed_rules);
		}
	}

	/**
	 * Add a admin menu.
	 */
	function wpoven_plugin_switcher_menu()
	{
		add_menu_page('WPOven Plugins', 'WPOven Plugins', '', 'wpoven', 'manage_options', plugin_dir_url(__FILE__) . '/img/logo.png');
		add_submenu_page('wpoven', 'Plugin Switcher', 'Plugin Switcher', 'manage_options', WPOVEN_PLUGIN_SWITCHER_SLUG);
	}

	function my_plugin_enqueue_scripts()
	{
		wp_enqueue_script(
			'wpoven-plugin-switcher-script',
			plugin_dir_url(__FILE__) . '/js/wpoven-plugin-switcher-admin.js',
			array('jquery'),
			null,
			true
		);

		// Pass the URL of the icon to JavaScript
		wp_localize_script('wpoven-plugin-switcher-script', 'myPluginData', array(
			'iconUrl' => plugin_dir_url(__FILE__) . '/img/copy.png'
		));
	}

	/**
	 * Hook to add the admin menu.
	 */
	public function admin_main(Wpoven_Plugin_Switcher $wpoven_plugin_switcher)
	{
		$this->_wpoven_plugin_switcher = $wpoven_plugin_switcher;
		add_action('admin_enqueue_scripts', array($this, 'my_plugin_enqueue_scripts'));
		add_action('admin_menu', array($this, 'wpoven_plugin_switcher_menu'));
		$this->setup_gui();
		add_action('redux/options/' . WPOVEN_PLUGIN_SWITCHER_SLUG . '/saved', array($this, 'wpoven_plugin_options_process'));
	}
}
