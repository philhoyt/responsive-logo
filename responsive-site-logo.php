<?php
/**
 * Plugin Name: Responsive Site Logo
 * Plugin URI:  https://github.com/philhoyt/responsive-logo
 * Description: A block for displaying a site logo that swaps at a configurable mobile breakpoint.
 * Version:     1.0.1
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
 * Returns sanitized inline SVG markup, or an empty string on failure.
 * Strips XML declarations, script tags, and inline event handlers.
 *
 * @param int    $attachment_id Attachment post ID.
 * @param string $alt           Alt text injected as aria-label.
 * @param string $css_class     Space-separated CSS classes for the <svg> element.
 * @param int    $max_width_px  Optional max-width in pixels (0 = none).
 * @return string
 */
function rsl_inline_svg( $attachment_id, $alt, $css_class, $max_width_px ) {
	$file = get_attached_file( $attachment_id );

	if ( ! $file || ! file_exists( $file ) ) {
		return '';
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a local file path returned by get_attached_file(); WP_Filesystem requires admin-page context with credential callbacks and is not suitable for block render callbacks.
	$svg = file_get_contents( $file );

	if ( ! $svg ) {
		return '';
	}

	// Strip XML declaration and doctype.
	$svg = preg_replace( '/<\?xml[^>]*\?>/i', '', $svg );
	$svg = preg_replace( '/<!DOCTYPE[^>]*>/i', '', $svg );

	// Strip script elements and on* event attributes.
	$svg = preg_replace( '/<script[\s\S]*?<\/script>/i', '', $svg );
	$svg = preg_replace( '/\s+on\w+\s*=\s*"[^"]*"/i', '', $svg );
	$svg = preg_replace( "/\\s+on\\w+\\s*=\\s*'[^']*'/i", '', $svg );

	// Inject class, aria-label, and optional max-width into the root <svg> tag.
	$style_attr = $max_width_px ? ' style="max-width:' . $max_width_px . 'px"' : '';
	$svg        = preg_replace(
		'/<svg(\s)/i',
		'<svg class="' . esc_attr( $css_class ) . '" role="img" aria-label="' . esc_attr( $alt ) . '"' . $style_attr . '$1',
		$svg,
		1
	);

	return trim( $svg );
}

/**
 * Outputs a logo — inline SVG when the file is an SVG, otherwise an <img> tag.
 *
 * @param string $url        Image URL.
 * @param string $alt        Alt / aria-label text.
 * @param int    $id         Attachment post ID.
 * @param string $type       MIME type.
 * @param string $css_class  CSS classes for the element.
 * @param int    $max_width  Optional max-width in pixels (0 = none).
 */
function rsl_render_logo( $url, $alt, $id, $type, $css_class, $max_width ) {
	if ( 'image/svg+xml' === $type && $id ) {
		$svg = rsl_inline_svg( $id, $alt, $css_class, $max_width );
		if ( $svg ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rsl_inline_svg() strips XML declarations, <script> elements, and on* event attributes, and escapes class/aria-label with esc_attr().
			echo $svg;
			return;
		}
	}

	if ( $max_width ) {
		printf(
			'<img src="%s" alt="%s" class="%s" style="max-width:%dpx" />',
			esc_url( $url ),
			esc_attr( $alt ),
			esc_attr( $css_class ),
			absint( $max_width )
		);
	} else {
		printf(
			'<img src="%s" alt="%s" class="%s" />',
			esc_url( $url ),
			esc_attr( $alt ),
			esc_attr( $css_class )
		);
	}
}

/**
 * Registers the block.
 */
function responsive_site_logo_block_init() {
	register_block_type( plugin_dir_path( __FILE__ ) . 'build/site-logo' );
}
add_action( 'init', 'responsive_site_logo_block_init' );
