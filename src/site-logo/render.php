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
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( 'Homepage', 'responsive-site-logo' ); ?>">
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
