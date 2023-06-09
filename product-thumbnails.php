<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$quickview          = isset( $_POST['quickview'] );
$layout             = $quickview ? '' : molla_option( 'single_product_layout' );
$layout_meta        = $quickview ? '' : get_post_meta( get_the_ID(), 'single_product_layout', true );
$post_attachment_id = $product->get_image_id();
$attachment_ids     = $product->get_gallery_image_ids();

if ( $layout_meta ) {
	if ( 'default' == $layout_meta ) {
		$layout = '';
	} else {
		$layout = $layout_meta;
	}
}


if ( 'masonry_sticky' != $layout ) {
    echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', molla_wc_get_gallery_image_html( $post_attachment_id, false, true ), $post_attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
}

// Video thumbnail code - Ark
global $product;
$video_url = get_post_meta( $product->get_id(), 'video_url', true );

if ( ! empty( $video_url ) ) {
    $video_thumbnail_url = '/wp-content/uploads/video-play-button.svg';
    ?>
    <div class="woocommerce-product-gallery__image video-thumbnail">
        <a href="<?php echo esc_url( home_url( '/wp-content/themes/av-test-theme/video-player.html?src=' . urlencode( $video_url ) ) ); ?>" class="video-popup" data-type="iframe">
            <img src="<?php echo esc_url( $video_thumbnail_url ); ?>" alt="Video thumbnail" />
        </a>
    </div>
    <?php
}

if ( $attachment_ids && $post_attachment_id ) {
    foreach ( $attachment_ids as $attachment_id ) {
        if ( 'masonry_sticky' == $layout ) {
            echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', molla_wc_get_gallery_image_html( $attachment_id, true ), $attachment_id, $layout ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
        } else {
            echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', molla_wc_get_gallery_image_html( $attachment_id, false ), $attachment_id, $layout );
        }
    }
}

// Add the Magnific Popup initialization code after the video thumbnail code (custom css added to style.css) - Ark
?>
<script>
(function($) {
    $(document).ready(function() {
        $('.video-popup').magnificPopup({
            type: 'iframe',
            iframe: {
                patterns: {
                    customsource: {
                        index: '',
                        src: '%id%'
                    }
                },
                srcAction: 'iframe_src'
            },
            mainClass: 'mfp-fade video-popup-container', // Add the custom class here
            removalDelay: 160,
            preloader: false,
            fixedContentPos: false,
			closeBtnInside: true
        });
    });
})(jQuery);
</script>

