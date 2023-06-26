<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $woocommerce_loop, $molla_settings;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$style            = $align = '';
$style            = wc_get_loop_prop( 'product_style' );
$align            = wc_get_loop_prop( 'product_align' );
$visible          = wc_get_loop_prop( 'visible' );
$quickview_pos    = wc_get_loop_prop( 'quickview_pos' );
$wishlist_pos     = wc_get_loop_prop( 'wishlist_pos' );
$layout_mode      = wc_get_loop_prop( 'layout_mode' ) ? wc_get_loop_prop( 'layout_mode' ) : molla_option( 'post_layout' );
$footer_action    = wc_get_loop_prop( 'footer_action' );
$body_action      = wc_get_loop_prop( 'body_action' );
$footer_out_body  = wc_get_loop_prop( 'footer_out_body' );
$vertical_animate = wc_get_loop_prop( 'product_vertical_animate' );
$icon             = 'yes' == wc_get_loop_prop( 'product_icon_hide' ) ? 'icon-hidden' : '';
$label            = 'yes' == wc_get_loop_prop( 'product_label_hide' ) ? 'label-hidden' : '';
$disable          = 'yes' == wc_get_loop_prop( 'disable_product_out' ) ? true : false;
$icon_top         = 'yes' == wc_get_loop_prop( 'action_icon_top' ) ? 'action-icon-top' : '';
$divider_type     = wc_get_loop_prop( 'divider_type' );
$link             = apply_filters( 'the_permalink', get_permalink() );
if ( ( molla_is_shop() || molla_is_in_category() ) && ! wc_get_loop_prop( 'widget' ) ) {
	$style = molla_get_loop_columns()['style'];
	if ( 'top' == $molla_settings['sidebar']['pos'] ) {
		$layout_mode = 'creative';
	}
}

$product_classes = molla_get_product_class( $style, $align );

if ( $disable && ! $product->is_in_stock() ) {
	$product_classes .= ' product-disabled';
}

$product_body_class = '';

$listed  = ( 'list' == $style ? true : false );
$full    = ( 'full' == $style ? true : false );
$classic = ( 'classic' == $style ? true : false );
$gallery = ( 'gallery-popup' == $style ? true : false );

if ( $full ) {
	$product_body_class .= ' t-x-' . wc_get_loop_prop( 't_x_pos' );
	$product_body_class .= ' t-y-' . wc_get_loop_prop( 't_y_pos' );
}

$product_cats = wc_get_product_taxonomy_class( $product->get_category_ids(), '' );
$product_cat  = '';
$s            = false;
foreach ( $product_cats as $cat ) {
	if ( ! $s ) {
		$product_cat .= substr( $cat, 1 );
		$s            = true;
	} else {
		$product_cat .= ' ' . substr( $cat, 1 );
	}
}

$wrap_class      = 'product-wrap';
$wrap_class     .= ' ' . $product_cat;
$wrap_class     .= 'creative' == $layout_mode ? ' grid-item' : '';
$wrap_class_temp = '';

$wrap_attr = '';

$action_class = 'product-action' . ( $divider_type ? ( ' divided ' . $divider_type ) : '' );
if ( ! $listed ) {
	$action_class .= ( $icon ? ( ' ' . $icon ) : '' ) . ( $label ? ( ' ' . $label ) : '' ) . ( $icon_top ? ( ' ' . $icon_top ) : '' );
}

if ( isset( $GLOBALS['molla_loop_index'] ) ) {
	$insert_id  = wc_get_loop_prop( 'banner_insert' );
	$repeaters  = wc_get_loop_prop( 'repeaters' );
	$current_id = $GLOBALS['molla_loop_index'];
	$wrap_attr  = ' data-grid-idx=' . $current_id;
	if ( isset( $repeaters['ids'][ $current_id ] ) ) {
		$wrap_class_temp                         .= ' ' . $repeaters['ids'][ $current_id ];
		$GLOBALS['molla_loop_product_thumb_size'] = $repeaters['images'][ $current_id  ];
	}

	// Print banner in products
	if ( (int) $insert_id == $current_id ) {
		$html = wc_get_loop_prop( 'product_banner' );
		if ( $html ) {
			$add_class = $wrap_class_temp . ' product-banner-wrap';

			echo '<div class="' . esc_attr( $wrap_class . $add_class ) . '"' . $wrap_attr . '>' . $html . '</div>';

			$GLOBALS['molla_loop_index'] ++;
			wc_set_loop_prop( 'product_banner', '' );
		}
	}

	if ( $current_id != $GLOBALS['molla_loop_index'] ) {
		$current_id      = $GLOBALS['molla_loop_index'];
		$wrap_class_temp = '';
		if ( isset( $repeaters['ids'][ $current_id ] ) ) {
			$wrap_class_temp                          = ' ' . $repeaters['ids'][ $current_id ];
			$GLOBALS['molla_loop_product_thumb_size'] = $repeaters['images'][ $current_id  ];
		}
		$wrap_attr = ' data-grid-idx=' . $current_id;
	}
	$GLOBALS['molla_loop_index'] ++;
}

if ( isset( $repeaters['ids'][0] ) ) {
	$wrap_class .= $wrap_class_temp . ' ' . $repeaters['ids'][0];
}

?>
<div class="<?php echo esc_attr( $wrap_class ); ?>"<?php echo ! $wrap_attr ? '' : ( ' ' . $wrap_attr ); ?>>
	<?php
	do_action( 'molla_wc_before_shop_product', $style );
	?>
	<div <?php wc_product_class( $product_classes, $product ); ?>>
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item' );
		if ( $listed ) :
			?>
		<div class="row">
			<div class="col-lg-3 col-md-3 col-6">
			<?php
		endif;
		?>
		<figure class="product-media">
			<a href="<?php echo esc_url( $link ); ?>">
			<?php
			/**
			 * Hook: woocommerce_before_shop_loop_item_title.
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );

			?>
			</a>
			<?php
			if ( 'center-thumbnail' == $quickview_pos ) :
				molla_quickview_html( 'zoom' );
			endif;
			if ( ! $listed && ! $full ) :
				if ( 'inner-thumbnail' == $wishlist_pos || 'inner-thumbnail' == $quickview_pos ) :
					?>
			<div class="product-action-vertical<?php echo esc_attr( $vertical_animate ? ( ' ' . $vertical_animate ) : '' ); ?>">
					<?php
					if ( 'inner-thumbnail' == $wishlist_pos ) :
						echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
					endif;
					if ( 'inner-thumbnail' == $quickview_pos ) :
						molla_quickview_html( 'icon' );
					endif;
					?>
			</div>
					<?php
					endif;
				if ( ! $footer_action && ! $body_action && ! $gallery ) :
					?>
			<div class="<?php echo esc_attr( $action_class ); ?>">
					<?php
					/**
					 * Hook: woocommerce_after_shop_loop_item.
					 *
					 * @hooked woocommerce_template_loop_product_link_close - 5
					 * @hooked woocommerce_template_loop_add_to_cart - 10
					 */
					do_action( 'woocommerce_after_shop_loop_item' );
					?>
			</div>
					<?php
				endif;
			endif;
			if ( is_array( $visible ) && $visible['deal'] ) {
				do_action( 'molla_woocommerce_sale_countdown' );
			}
			?>
		</figure>
		<?php
		if ( $listed ) :
			?>
			</div>
			<?php
		endif;
		if ( $listed ) :
			?>
		<div class="col-lg-6 col-md-6 order-last">
			<?php
		endif;
		?>
		<div class="product-body<?php echo esc_attr( $product_body_class ); ?>">
			<?php

			if ( 'no-overlay' == $style ) :
				?>
				<div class="<?php echo esc_attr( $action_class ); ?>">
				<?php
					do_action( 'woocommerce_after_shop_loop_item' );
				?>
				</div>
				<?php
			endif;

			if ( $listed ) {
				add_filter(
					'yith_wcwl_button_label',
					function() {
						return esc_html__( 'wishlist', 'molla' );
					}
				);
				add_filter(
					'yith_wcwl_browse_wishlist_label',
					function() {
						return '<span>' . esc_html__( 'wishlist', 'molla' ) . '</span>';
					}
				);

				do_action( 'woocommerce_shop_loop_item_title' );
				?>
				<div class="product-content">
					<?php echo apply_filters( 'molla_woocommerce_loop_content_excerpt', $product->get_short_description() ); ?>
				</div>
				<?php
				/**
				 * Hook: molla_woocommerce_product_listed_attrs.
				 *
				 * @hooked molla_woocommerce_print_pickable_attrs - 5
				 */
				do_action( 'molla_woocommerce_product_listed_attrs' );
			} else {
				if ( 'slide' == $style ) :
					?>
					<div class="<?php echo esc_attr( $action_class ); ?>">
					<?php
						do_action( 'woocommerce_after_shop_loop_item' );
					?>
					</div>
					<?php
				endif;

				/**
				 * Hook: woocommerce_shop_loop_item_title.
				 *
				 * @hooked woocommerce_template_loop_product_title - 10
				 */
				do_action( 'woocommerce_shop_loop_item_title' );

				/**
				 * Hook: woocommerce_after_shop_loop_item_title.
				 *
				 * @hooked woocommerce_template_loop_price - 10
				 * @hooked woocommerce_template_loop_rating - 15
				 */
				if ( $classic || 'popup' == $style || 'slide' == $style ) :
					remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );
				endif;
				do_action( 'woocommerce_after_shop_loop_item_title' );

				if ( $footer_action && ! $footer_out_body ) :
					if ( $classic ) :
						?>
				<div class="product-footer">
						<?php
						if ( is_array( $visible ) && $visible['rating'] ) {
							woocommerce_template_loop_rating();
						}
					endif;
					?>
					<div class="<?php echo esc_attr( $action_class ); ?>">
						<?php
						do_action( 'woocommerce_after_shop_loop_item' );

						if ( 'inner-body-footer' == $wishlist_pos ) {
							echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
						}
						?>
					</div>
					<?php
					if ( $classic ) :
						?>
				</div>
						<?php
						endif;
				endif;

				if ( $full || $gallery ) :
					?>
					<div class="<?php echo esc_attr( $action_class ); ?>">
					<?php
					do_action( 'woocommerce_after_shop_loop_item' );
					if ( $gallery && 'inner-gallery' == $quickview_pos ) :
						molla_quickview_html( '' );
					endif;
					?>
					</div>
					<?php
				endif;
			}
			?>
		</div>
		<?php
		if ( $listed ) :
			?>
		</div>
		<div class="col-lg-3 col-md-3 col-6 order-md-last order-lg-last">
			<div class="product-list-action">
				<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>
				<div class="<?php echo esc_attr( $action_class ); ?>">
				<?php
				if ( 'inner-list' == $quickview_pos ) :
					molla_quickview_html( '' );
				endif;

				if ( 'inner-list' == $wishlist_pos ) :
					echo '<span class="divider-dotted"></span>';
					echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
				endif;
				?>
				</div>
				<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
			</div>
		</div>
			<?php
		endif;
		if ( ( $footer_action && $footer_out_body ) || $body_action ) :
			?>
		<div class="product-footer">
			<?php do_action( 'molla_woocommerce_loop_footer_content' ); ?>
			<?php if ( ! $body_action ) : ?>
				<div class="<?php echo esc_attr( $action_class ); ?>">
					<?php
					do_action( 'woocommerce_after_shop_loop_item' );
					if ( 'inner-footer' == $quickview_pos ) :
						molla_quickview_html( '' );
					endif;
					?>
				</div>
			<?php endif; ?>
		</div>
			<?php
		endif;

		if ( $listed ) :
			?>
		</div>
			<?php
		endif;
		?>
	</div>
	<?php
	do_action( 'molla_wc_after_shop_product', $style );
	?>
</div>
