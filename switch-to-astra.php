<?php
/**
 * Plugin Name: Switch to Astra
 * Plugin URI: https://wpastra.com/
 * Description: This plugin essentially helps you make your existing website compatible with Page Builders when you switch to Astra theme.
 * Version: 1.0.1
 * Author: Brainstorm Force
 * Author URI: https://www.brainstormforce.com
 * Text Domain: switch-to-astra
 *
 * @package Switch_to_Astra
 */

/**
 * Set constants.
 */
define( 'SWITCH_TO_ASTRA_FILE', __FILE__ );
define( 'SWITCH_TO_ASTRA_BASE', plugin_basename( SWITCH_TO_ASTRA_FILE ) );
define( 'SWITCH_TO_ASTRA_DIR', plugin_dir_path( SWITCH_TO_ASTRA_FILE ) );
define( 'SWITCH_TO_ASTRA_URI', plugins_url( '/', SWITCH_TO_ASTRA_FILE ) );
define( 'SWITCH_TO_ASTRA_VER', '1.0.1' );

/**
 * Extensions
 */
require_once SWITCH_TO_ASTRA_DIR . 'classes/class-switch-to-astra.php';
