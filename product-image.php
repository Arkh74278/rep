<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;
global $thumbnail_image;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$attachment_ids    = $product->get_gallery_image_ids();
/// new code 10.06.23
// Check if video exists
$video = get_post_meta( get_the_ID(), 'video', true );

if ( $video ) {
    // Insert placeholder image into attachment ids
    array_unshift( $attachment_ids, '8885' );
}

if ( $attachment_ids && $product->get_image_id() ) {
    foreach ( $attachment_ids as $attachment_id ) {
        echo wc_get_gallery_image_html( $attachment_id, true );
    }
}
// end of new code 10.06.23


$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);

$thumbnail_image = false;
$quickview       = isset( $_POST['quickview'] );
$layout          = $quickview ? '' : molla_option( 'single_product_layout' );
$layout_meta     = $quickview ? '' : get_post_meta( get_the_ID(), 'single_product_layout', true );
$widget_mode     = apply_filters( 'molla_is_single_product_widget', false );


if ( $layout_meta ) {
	if ( 'default' == $layout_meta ) {
		$layout = '';
	} else {
		$layout = $layout_meta;
	}
}

$has_thumbnail = false;
if ( ! $layout || 'extended' == $layout || 'boxed' == $layout || 'full' == $layout ) {
	$has_thumbnail = true;
}

$has_thumbnail = apply_filters( 'molla_is_hide_single_product_thumbnail', $has_thumbnail );

$thumb_direct = molla_option( 'single_product_image' );
$meta         = get_post_meta( get_the_ID(), 'single_product_image', true );
if ( $meta ) {
	$thumb_direct = $meta;
}

if ( $widget_mode ) {
	$thumb_direct = ( 'default' == $widget_mode ) ? 'vertical' : 'horizontal';
}

$img_nav_pos = '';
if ( ! $quickview && $has_thumbnail ) {
	if ( 'horizontal' == $thumb_direct ) {
		$img_nav_pos = ' thumb-horizontal';
	} else {
		$img_nav_pos = ' thumb-vertical';
	}
}

$separated = false;
if ( 'sticky' == $layout ) {
	$separated = true;
}

$thumb_add_class = '';
$thumb_data      = '';
if ( ! $quickview || 'horizontal' == molla_option( 'quickview_style' ) ) {
	$thumb_add_class = ' owl-carousel owl-simple owl-nav-inside owl-full c-xs-3 c-sm-4 sp-10';
	$thumb_data      = ' data-toggle="owl"';
}
// Change size of single product thumbs on mobile - Ark
$thumb_options['loop']       = false;
$thumb_options['nav']        = true;
$thumb_options['dots']       = false;
$thumb_options['margin']     = 10;
$thumb_options['responsive'] = array(
	0   => array(
		'items' => 5,
	),
);

$thumb_data .= ' data-owl-options=' . json_encode( $thumb_options );
?>     
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) . ( 'sticky' == $layout ? ' product-gallery-separated' : '' ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
	<?php
	if ( $has_thumbnail ) :
		?>
	<div class="row<?php echo esc_attr( $img_nav_pos ); ?>">
		<?php
	endif;

	do_action( 'molla_woocommerce_after_single_image' );

	if ( 'sticky' != $layout && 'masonry_sticky' != $layout && 'custom' != $layout || $quickview ) :
		$options['loop']   = false;
		$options['nav']    = true;
		$options['dots']   = false;
		$options['items']  = 1;
		$options['margin'] = 0;
		$add_class         = 'owl-nav-inside';
		$args              = array(
			576 => array(
				'items' => '',
			),
			768 => array(
				'items' => '',
			),
			992 => array(
				'items' => $quickview ? 1 : (int) molla_option( 'single_product_image_col' ),
			),
		);
		if ( 'gallery' == $layout ) {
			$options['margin']     = 10;
			$add_class             = molla_option( 'single_product_image_slider_nav' ) . ' ' . molla_option( 'single_product_image_slider_nav_type' );
			$options['responsive'] = molla_carousel_options( $args );
			$add_class            .= molla_carousel_responsive_classes( $options['responsive'] );
		}
		if ( 'gallery' != $layout ) {
			$add_class .= ' c-xs-1';
		}
		$add_class .= ' sp-10';

		?>
	<div class="owl-carousel owl-simple product-gallery-carousel<?php echo ' ' . esc_attr( $add_class ); ?>" data-toggle="owl" data-owl-options="<?php echo esc_attr( json_encode( $options ) ); ?>">
		<?php
	endif;

	if ( ! $quickview ) :
		?>
		<figure class="woocommerce-product-gallery__wrapper product-main-image">
	
		<?php
		if ( $product->get_image_id() ) {
			$html = molla_wc_get_gallery_image_html( $post_thumbnail_id, true, true );
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id, -1, $widget_mode );// phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
		?>
		</figure>
		
	
		<?php
		if ( 'masonry_sticky' != $layout && 'custom' != $layout ) :
			if ( $attachment_ids && $post_thumbnail_id ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$attach_html = molla_wc_get_gallery_image_html( $attachment_id, true );
					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $attach_html, $attachment_id, -1, $widget_mode ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
				}
			}
		endif;
	else :
		$thumbnail_image = true;
		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', molla_wc_get_gallery_image_html( $post_thumbnail_id, true, true ), $post_attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		if ( $attachment_ids && $post_thumbnail_id ) {
			foreach ( $attachment_ids as $attachment_id ) {
				$attach_html = molla_wc_get_gallery_image_html( $attachment_id, true );
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $attach_html, $attachment_id, -1, $widget_mode ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			}
		}
	endif;

	if ( 'sticky' != $layout && 'masonry_sticky' != $layout && 'custom' != $layout || $quickview ) :
		?>
	</div>
		<?php
	endif;

	if ( ! apply_filters( 'molla_is_hide_single_product_thumbnail', false ) ) :
		if ( $has_thumbnail ) :
			?>
		<div id="product-zoom-gallery" class="product-image-gallery">
			<div class="thumbnails-wrap">
				<div class="thumbnails-outer<?php echo esc_attr( $thumb_add_class ); ?>"<?php echo '' . $thumb_data; ?>>
				<?php
		endif;

		$thumbnail_image = true;
		if ( 'masonry_sticky' == $layout ) :
			$thumbnail_image = false;
			?>
		<div class="product-gallery-masonry" data-toggle="isotope" data-isotope-options='{"itemSelector": ".woocommerce-product-gallery__image"}'>
			<?php
		endif;

		if ( 'sticky' != $layout && 'gallery' != $layout ) {
			do_action( 'woocommerce_product_thumbnails' );
		}

		if ( 'masonry_sticky' == $layout ) :
			?>
		</div>
			<?php
			endif;

		if ( $has_thumbnail ) :
			?>
				</div>
				<div class="vertical-nav">
					<button type="button" class="nav-prev">
						<i class="icon-angle-up"></i>
					</button>
					<button type="button" class="nav-next">
						<i class="icon-angle-down"></i>
					</button>
				</div>
			</div>
		</div>
			<?php
			endif;
		endif;

	if ( 'sticky' != $layout && 'masonry_sticky' != $layout ) :
		?>
	</div>
		<?php
	endif;
	?>
</div>
<?php
