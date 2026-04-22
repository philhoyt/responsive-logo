<?php
/**
 * Server-side render for the Responsive Site Logo block.
 *
 * @package ResponsiveSiteLogo
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (unused).
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$desktop_url  = ! empty( $attributes['desktopLogoUrl'] ) ? $attributes['desktopLogoUrl'] : '';
$desktop_alt  = isset( $attributes['desktopLogoAlt'] ) ? $attributes['desktopLogoAlt'] : '';
$desktop_id   = ! empty( $attributes['desktopLogoId'] ) ? absint( $attributes['desktopLogoId'] ) : 0;
$desktop_type = isset( $attributes['desktopLogoType'] ) ? $attributes['desktopLogoType'] : '';
$mobile_url   = ! empty( $attributes['mobileLogoUrl'] ) ? $attributes['mobileLogoUrl'] : '';
$mobile_alt   = isset( $attributes['mobileLogoAlt'] ) ? $attributes['mobileLogoAlt'] : '';
$mobile_id    = ! empty( $attributes['mobileLogoId'] ) ? absint( $attributes['mobileLogoId'] ) : 0;
$mobile_type  = isset( $attributes['mobileLogoType'] ) ? $attributes['mobileLogoType'] : '';
$breakpoint   = isset( $attributes['breakpoint'] ) ? absint( $attributes['breakpoint'] ) : 768;
$max_width    = ! empty( $attributes['maxWidth'] ) ? absint( $attributes['maxWidth'] ) : 0;
$link_home    = isset( $attributes['linkToHome'] ) ? (bool) $attributes['linkToHome'] : true;

if ( ! $desktop_url && ! $mobile_url ) {
	return;
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

$unique_id = wp_unique_id( 'rsl-' );
?>
<style>
	#<?php echo esc_attr( $unique_id ); ?> .rsl-logo--mobile { display: none; }
	@media (max-width: <?php echo absint( $breakpoint ); ?>px) {
		#<?php echo esc_attr( $unique_id ); ?> .rsl-logo--desktop { display: none; }
		#<?php echo esc_attr( $unique_id ); ?> .rsl-logo--mobile { display: block; }
	}
</style>
<div id="<?php echo esc_attr( $unique_id ); ?>" <?php echo get_block_wrapper_attributes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() is a WP core function that builds and sanitizes its own output; it cannot be double-escaped without corrupting the HTML. ?>>
	<?php if ( $link_home ) : ?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( 'Homepage', 'responsive-logo' ); ?>">
	<?php endif; ?>

	<?php if ( $desktop_url ) : ?>
		<?php rsl_render_logo( $desktop_url, $desktop_alt, $desktop_id, $desktop_type, 'rsl-logo rsl-logo--desktop', $max_width ); ?>
	<?php endif; ?>

	<?php if ( $mobile_url ) : ?>
		<?php rsl_render_logo( $mobile_url, $mobile_alt, $mobile_id, $mobile_type, 'rsl-logo rsl-logo--mobile', $max_width ); ?>
	<?php endif; ?>

	<?php if ( $link_home ) : ?>
	</a>
	<?php endif; ?>
</div>
