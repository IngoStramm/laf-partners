<?php
/**
 * Plugin Name: Laf Partners
 * Plugin URI: https://agencialaf.com
 * Description: Plugin do Laf Partners
 * Version: 2.0.1
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

require 'plugin-update-checker-4.10/plugin-update-checker.php';
$updateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://raw.githubusercontent.com/IngoStramm/laf-partners/master/info.json',
    __FILE__,
    'laf-partners'
);