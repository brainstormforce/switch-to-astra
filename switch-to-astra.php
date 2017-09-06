<?php
/**
 * Plugin Name: Switch to Astra
 * Plugin URI: https://wpastra.com/
 * Description: Plugin Desctiption.
 * Version: 1.0.0
 * Author: Brainstorm Force
 * Author URI: http://www.brainstormforce.com
 * Text Domain: switch-to-astra
 *
 * @package Switch to Astra
 */

/**
 * Set constants.
 */
define( 'SWITCH_TO_ASTRA_FILE', __FILE__ );
define( 'SWITCH_TO_ASTRA_BASE', plugin_basename( SWITCH_TO_ASTRA_FILE ) );
define( 'SWITCH_TO_ASTRA_DIR', plugin_dir_path( SWITCH_TO_ASTRA_FILE ) );
define( 'SWITCH_TO_ASTRA_URI', plugins_url( '/', SWITCH_TO_ASTRA_FILE ) );
define( 'SWITCH_TO_ASTRA_VER', '1.0.0' );

/**
 * Extensions
 */
require_once SWITCH_TO_ASTRA_DIR . 'classes/class-switch-to-astra.php';
