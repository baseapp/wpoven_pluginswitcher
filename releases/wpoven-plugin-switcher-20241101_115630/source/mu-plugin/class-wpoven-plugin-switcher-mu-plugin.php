<?php

/**
 * File to create must-use plugin for Wpoven_Plugin_Switcher.
 *
 * @link       https://wpoven.com
 * @since      1.0.0
 *
 * @package    Wpoven_Plugin_Switcher
 * @subpackage Wpoven_Plugin_Switcher/mu-plugin
 */


if (!defined('WPOVEN_PLUGIN_SWITCHER_SLUG'))
    define('WPOVEN_PLUGIN_SWITCHER_SLUG', 'wpoven-plugin-switcher');
class WPOvenPluginSwitcher
{
    public function __construct()
    {
        // Constructor code, if any
    }

    function matchInput($input, $pattern)
    {
        // Check if pattern is a valid regex
        // We assume a valid regex pattern is enclosed with delimiters
        if (@preg_match($pattern, null) !== FALSE) {
            // It's a regex pattern, perform regex match
            return preg_match($pattern, $input) ? true : false;
        } else {
            // It's a string, perform string match
            return strpos($input, $pattern) !== false ? true : false;
        }
    }

    function processPluginList($orignalList, $userList, $activate = false)
    {
        if ($activate) {
            return $userList;
        }
        return array_diff($orignalList, $userList);
    }


    function processRules($plugins)
    {
        $rules = get_option(WPOVEN_PLUGIN_SWITCHER_SLUG . '-rules');
        $rulePlugins = array();
        $current_page_url = home_url(add_query_arg(NULL, NULL));
        $current_page_url = trim($current_page_url, "/");
        $current_page_slug = basename(wp_parse_url($current_page_url, PHP_URL_PATH));
        $matchFound = false;

        if (is_array($rules)) {
            foreach ($rules as $rule) {
                if (!isset($rule['status'])) {   // if (!$rule['status']) {
                    continue;
                }
                switch ($rule['type']) {
                    case 'page':
                    case 'post':
                        $item = get_page_by_path($current_page_slug, OBJECT, $rule['type']);
                        $key = $rule['type'] . 's';

                        if (isset($item->ID) && isset($rule[$key]) && in_array($item->ID, $rule[$key])) {
                            if (isset($rule['plugins']) && !empty($rule['plugins'])) {
                                $rulePlugins = $this->processPluginList($plugins, $rule['plugins'], $rule['plugin_status']);
                                $matchFound = true;
                            }
                        }
                        break;

                    case 'home_page':
                        if ((home_url() == $current_page_url) && (isset($rule['plugins']) && !empty($rule['plugins']))) {
                            $rulePlugins = $this->processPluginList($plugins, $rule['plugins'], $rule['plugin_status']);
                            $matchFound = true;
                        }
                        break;

                    case 'url':

                        if (isset($rule['url']) && !empty($rule['url'])) {

                            if (strpos($current_page_url, 'plugins.php')) {
                                break;
                            }

                            if (isset($rule['post_match']) && !empty($rule['post_match'])) {
                                $post_match = $rule['post_match'];

                                if ($post_match && is_array($_POST)) {
                                    $result = in_array($post_match, $_POST);
                                }

                                if ((isset($result) && $result) && (isset($rule['plugins']) && !empty($rule['plugins']))) {
                                    $rulePlugins = $this->processPluginList($plugins, $rule['plugins'], $rule['plugin_status']);
                                    $matchFound = true;
                                    break;
                                }
                            }

                            if ($this->matchInput($current_page_url, $rule['url']) && (isset($rule['plugins']) && !empty($rule['plugins']))) {
                                $rulePlugins = $this->processPluginList($plugins, $rule['plugins'], $rule['plugin_status']);
                                $matchFound = true;
                                break;
                            }
                        }
                        break;
                }
            }
        }

        if (!$matchFound) {
            $rulePlugins = $plugins;
        }

        return $rulePlugins;
    }
}

// Instantiate the class
$wpoven_plugin_switcher = new WPOvenPluginSwitcher();

add_filter('option_active_plugins', array($wpoven_plugin_switcher, 'processRules'));
