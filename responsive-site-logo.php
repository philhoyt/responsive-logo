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
 * Text Domain: responsive-logo
 *
 * @package ResponsiveSiteLogo
 */

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Updates are delivered from GitHub releases via Plugin Update Checker.
require_once __DIR__ . '/lib/plugin-update-checker/plugin-update-checker.php';

$responsive_site_logo_update_checker     = PucFactory::buildUpdateChecker(
	'https://github.com/philhoyt/responsive-logo/',
	__FILE__,
	'responsive-site-logo'
);
$responsive_site_logo_update_checker_api = $responsive_site_logo_update_checker->getVcsApi();
if ( method_exists( $responsive_site_logo_update_checker_api, 'enableReleaseAssets' ) ) {
	// Only a .zip asset counts, and a release without one is skipped. The
	// default PREFER_RELEASE_ASSETS falls back to GitHub's source archive,
	// which installs as <Repo>-<tag>/ and breaks the plugin directory.
	$responsive_site_logo_update_checker_api->enableReleaseAssets( '/\.zip$/', $responsive_site_logo_update_checker_api::REQUIRE_RELEASE_ASSETS );
}

/**
 * Registers the block.
 */
function responsive_site_logo_block_init() {
	register_block_type( plugin_dir_path( __FILE__ ) . 'build/site-logo' );
}
add_action( 'init', 'responsive_site_logo_block_init' );
