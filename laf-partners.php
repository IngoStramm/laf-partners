<?php
/**
 * Plugin Name: Laf Partners
 * Plugin URI: https://agencialaf.com
 * Description: Plugin do Laf Partners
 * Version: 1.3.3
 * Author: Ingo Stramm
 * Author URI: https://agencialaf.com
 * Text Domain: lp
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

define( 'LP_DIR', plugin_dir_path( __FILE__ ) );
define( 'LP_URL', plugin_dir_url( __FILE__ ) );

require_once 'lp-functions.php';
require_once 'lp-metaboxes.php';
require_once 'lp-scripts.php';