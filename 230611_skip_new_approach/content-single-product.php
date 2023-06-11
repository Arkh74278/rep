<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
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

global $product;


if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}

$single_id    = get_the_ID();
$quickview    = isset( $_POST['quickview'] );
$layout       = $quickview ? '' : molla_option( 'single_product_layout' );
$layout_meta  = $quickview ? '' : get_post_meta( $single_id, 'single_product_layout', true );
$center_mode  = molla_option( 'single_product_center' );
$thumb_direct = molla_option( 'single_product_image' );
$meta         = get_post_meta( $single_id, 'single_product_center', true );


if ( $meta ) {
	if ( 'on' == $meta ) {
		$center_mode = true;
	} else {
		$center_mode = false;
	}
}

$meta = get_post_meta( $single_id, 'single_product_image', true );
if ( $meta ) {
	$thumb_direct = $meta;
}

if ( $layout_meta ) {
	if ( 'default' == $layout_meta ) {
		$layout = '';
	} else {
		$layout = $layout_meta;
	}
}

if ( apply_filters( 'molla_is_single_product_widget', false ) ) {
	if ( 'default' == apply_filters( 'molla_is_single_product_widget', false ) ) {
		$layout = '';
	} else {
		$layout = 'extended';
	}
	$view_type = 'widget';
} else {
	$view_type = '';
}

if ( ! $view_type ) {

	/**
	 * Hook: woocommerce_before_single_product.
	 *
	 * @hooked wc_print_notices - 10
	 */
	do_action( 'woocommerce_before_single_product' );
}

if ( 'gallery' == $layout ) {
	$center_mode = true;
}

if ( ! $layout || 'extended' == $layout || 'boxed' == $layout || 'full' == $layout ) {
	$thumb_gallery = true;
}

$variable = $product->is_type( 'variable' );

if ( 'gallery' == $layout && $variable ) {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	remove_action( 'woocommerce_single_product_summary', 'molla_woocommerce_single_product_deal', 25 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

	add_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_title', 5 );
	add_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_rating', 10 );
	add_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_price', 10 );
	add_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_excerpt', 20 );
	add_action( 'woocommerce_before_variations_form', 'molla_woocommerce_single_product_deal', 25 );
	add_action( 'woocommerce_after_variations_form', 'woocommerce_template_single_meta', 40 );
}

if ( ! is_user_logged_in() && molla_option( 'catalog_mode' ) ) {
	do_action( 'molla_single_product_init', $layout, $variable );
}

$img_col = intval( molla_option( 'single_product_image_wrap_col' ) );

if ( $quickview ) {
	$brkpoint = 'col-lg-';
	$img_col  = 6;
} else {
	$brkpoint = 'col-md-';
}

if ( ! empty( $posts ) && ! molla_ajax() ) {
	$name = molla_option( 'single_product_layout_slug' );

	global $molla_settings;
	if ( ! empty( $molla_settings['product_layout'] ) ) {
		$name = $molla_settings['product_layout'];
	}

	// If in product_layout template or elementor preview page, show single product with new layout builder
	// if ( class_exists( 'Molla_Custom_Product' ) && Molla_Custom_Product::get_instance()->product_layout || molla_is_elementor_preview() ) {
	// 	$name = $posts[0]->ID;
	// }

	if ( molla_is_elementor_preview() ) { // If in elementor preview page, show single product with new layout builder
		$name = $posts[0]->ID;
	}

	if ( defined( 'MOLLA_ELEMENTOR_TEMPLATES' ) && ( 'custom' == $layout || ! empty( $molla_settings['product_layout'] ) || 'product_layout' == $posts[0]->post_type ) ) {
		?>
		<div id="product-<?php echo intval( $product->get_id() ); ?>" <?php wc_product_class( '', $product ); ?>>
			<div class="product-intro">
			<?php
			//          do_action( 'molla_woo_before_single_product_content' );
			//echo esc_attr( apply_filters( 'molla_wc_single_product_classes', 'custom-product' ) );
			if ( function_exists( 'molla_print_custom_post' ) ) {
				molla_print_custom_post( 'product_layout', $name );
			}

			// do_action( 'molla_woo_after_single_product_content', $layout );

			//var_dump( $name );

			?>
			</div>
		</div>
		<?php
		do_action( 'woocommerce_after_single_product' );
		return;
	}
}

$intro_class = '';

if ( $layout ) {
	$intro_class = ' ' . $layout . '-product';
	if ( 'masonry_sticky' == $layout ) {
		wp_enqueue_script( 'isotope-pkgd' );
		$intro_class .= ' sticky-product';
	}
} else {
	$intro_class = ' default-product';
}

if ( $center_mode && ! $quickview ) {
	$intro_class .= ' center-mode';
}

$intro_class .= ' ' . $thumb_direct;

if ( $view_type ) {
	echo '<div class="woocommerce single-product-widget">';
}
?>

<div id="product-<?php echo intval( $quickview ? $product->get_id() : the_ID() ); ?>" <?php wc_product_class( ( isset( $thumb_gallery ) ? 'gallery-vertical' : '' ), $product ); ?>>
	<div class="product-intro <?php echo esc_attr( apply_filters( 'molla_wc_single_product_classes', $intro_class ) ); ?>">
	<?php

	do_action( 'molla_woo_before_single_product_content' );

	if ( 'gallery' != $layout ) :
		?>
	<div class="row<?php echo esc_attr( 'sticky' == $layout || 'masonry_sticky' == $layout ? ' sticky-sidebar-wrapper' : '' ); ?>">
		<div class="<?php echo esc_attr( apply_filters( 'molla_single_product_image_columns', $brkpoint . esc_attr( $img_col ) ) ); ?>">
		<?php
	endif;
		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @removed woocommerce_show_product_sale_flash - 10
		 * @hooked woocommerce_show_product_images - 20
		 */
		do_action( 'woocommerce_before_single_product_summary' );

	if ( 'gallery' != $layout ) :
		?>
		</div>
		<div class="<?php echo esc_attr( apply_filters( 'molla_single_product_image_columns', $brkpoint . esc_attr( 12 - $img_col ) ) ); ?>">
		<?php
		endif;
		$summary_class = 'summary entry-summary';
	if ( 'sticky' == $layout || 'masonry_sticky' == $layout ) {
		$summary_class .= ' sticky-sidebar';
	} elseif ( 'gallery' == $layout && ! $variable ) {
		$summary_class .= ' row';
	}
	?>
			<div class="<?php echo esc_attr( $summary_class ); ?>">
				<?php
				/**
				 * Hook: woocommerce_single_product_summary.
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_description - 20
				 * @hooked molla_woocommerce_single_product_deal - 25
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 * @hooked WC_Structured_Data::generate_product_data() - 60
				 */

				if ( 'gallery' == $layout && ! $variable ) :
					add_action( 'woocommerce_single_product_summary', 'molla_single_product_1st_wrap_start', 1 );
					add_action( 'wooc ommerce_single_product_summary', 'molla_single_product_1st_end_2nd_start', 21 );
					add_action( 'woocommerce_single_product_summary', 'molla_single_product_2nd_wrap_end', 61 );
				endif;
				do_action( 'woocommerce_single_product_summary' );

				?>
			</div>
			<?php

			if ( 'gallery' != $layout ) :
				?>
		</div>
	</div>
						<?php
			endif;
			if ( 'sticky' == $layout || 'masonry_sticky' == $layout ) :
				?>
		<hr class="mt-10 mb-10">
				<?php
		endif;

			do_action( 'molla_woo_after_single_product_content', $layout );

			?>
	</div>
	<?php
	if ( ! $quickview && 0 == strlen( $view_type ) ) {
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 *
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	}
	?>
</div>

<?php
if ( $view_type ) {
	echo '</div>';
}

do_action( 'woocommerce_after_single_product' );
