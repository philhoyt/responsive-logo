<?php
/**
 * Plugin Name: Responsive Site Logo
 * Plugin URI:  https://github.com/philhoyt/responsive-site-logo
 * Description: A block for displaying a site logo that swaps at a configurable mobile breakpoint.
 * Version:     1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author:      Phil Hoyt
 * Author URI:  https://philhoyt.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: responsive-site-logo
 *
 * @package ResponsiveSiteLogo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the block.
 */
function responsive_site_logo_block_init() {
	register_block_type( plugin_dir_path( __FILE__ ) . 'build/site-logo' );
}
add_action( 'init', 'responsive_site_logo_block_init' );
