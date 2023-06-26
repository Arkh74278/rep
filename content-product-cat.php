<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce_loop, $molla_settings;

$style = wc_get_loop_prop( 'category_style' ) ? wc_get_loop_prop( 'category_style' ) : molla_option( 'post_category_type' );
$align = wc_get_loop_prop( 'content_align' );

$layout_mode = wc_get_loop_prop( 'layout_mode' );
$grid_type   = wc_get_loop_prop( 'grid_type' );

if ( ! $layout_mode && class_exists( 'WooCommerce' ) && molla_is_shop() || molla_is_in_category() ) {
	$layout_mode = molla_option( 'post_layout' );
	if ( 'top' == $molla_settings['sidebar']['pos'] ) {
		$layout_mode = 'creative';
	}
	if ( molla_is_shop() && 'creative' == molla_option( 'post_layout' ) ) {
		$layout_mode = 'creative';
		$grid_type   = molla_creative_grid_layout( molla_option( 'post_creative_layout' ) );
	}
}

$cat_col_classes = '';
if ( 'creative' == $layout_mode && $grid_type ) {

	$item_idx = wc_get_loop_prop( 'item_idx' );

	if ( '' === $item_idx ) {
		$item_idx = 0;
	}
	if ( ! empty( $grid_type[ $item_idx ] ) && $grid_type[ $item_idx ] ) {
		foreach ( $grid_type[ $item_idx ] as $key => $val ) {
			if ( 'size' != $key ) {
				$cat_col_classes .= $key . '-' . $val . ' ';
			}
		}
		if ( 'woocommerce_thumbnail' == wc_get_loop_prop( 'image_size' ) ) {
			wc_set_loop_prop( 'image_size', $grid_type[ $item_idx ]['size'] );
		}
	}
	$item_idx ++;
	wc_set_loop_prop( 'item_idx', $item_idx );
}
$cat_classes = molla_get_category_class( $style, $align );

$content_class = molla_get_cat_content_class( $style );

if ( 'creative' != $layout_mode ||
	( isset( $grid_type ) && ! $grid_type ) ||
	( $item_idx && ! empty( $grid_type[ $item_idx - 1 ] ) ) ) :

	?>

<div class="cat-wrap <?php echo apply_filters( 'molla_wc_shop_cat_classes', esc_attr( $cat_col_classes . ( 'creative' == $layout_mode ? ' grid-item ' : ' ' ) . $category->slug ), $layout_mode ); ?>">
	<?php
	do_action( 'molla_wc_before_shop_cat' );
	?>
	<div <?php wc_product_cat_class( $cat_classes, $category ); ?>> 
		<figure class="cat_thumb">
		<?php
		/**
		 * woocommerce_before_subcategory hook.
		 *
		 * @hooked woocommerce_template_loop_category_link_open - 10
		 */
		do_action( 'woocommerce_before_subcategory', $category );
		/**
		 * woocommerce_before_subcategory_title hook.
		 *
		 * @hooked woocommerce_subcategory_thumbnail - 10
		 */
		do_action( 'woocommerce_before_subcategory_title', $category );

		/**
		 * woocommerce_after_subcategory hook.
		 *
		 * @hooked woocommerce_template_loop_category_link_close - 10
		 */
		do_action( 'woocommerce_after_subcategory', $category );
		?>
		</figure>
		<div class="cat-content <?php echo esc_attr( $content_class ); ?>">
		<?php

		/**
		 * woocommerce_shop_loop_subcategory_title hook.
		 *
		 * @hooked woocommerce_template_loop_category_title - 10
		 */
		do_action( 'woocommerce_shop_loop_subcategory_title', $category );

		/**
		 * woocommerce_after_subcategory_title hook.
		 */
		do_action( 'woocommerce_after_subcategory_title', $category );

		/**
		 * category link
		 */
		if ( 'yes' != wc_get_loop_prop( 'hide_link' ) ) :
			do_action( 'molla_woocommerce_shop_link', $category );
		endif;

		?>
		</div>
	</div>
	<?php
	do_action( 'molla_wc_after_shop_cat' );
	?>
</div>

	<?php
endif;
