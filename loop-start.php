<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $molla_settings;

$add_class = $options = $plugin_options_escaped = $layout_mode = $layout_style = $product_hover = '';

$layout_style = wc_get_loop_prop( 'layout_style' );
$loop_changed = ! wc_get_loop_prop( 'widget' ) || ! wc_get_loop_prop( 'type' );

// For Cross-Sells Products
if ( is_cart() ) {
	wc_set_loop_prop( 'layout_mode', molla_option( 'single_related_layout_type' ) );
	wc_set_loop_prop( 'columns', molla_option( 'single_related_cols' ) );
	wc_set_loop_prop( 'product_style', molla_option( 'post_product_type' ) );
	wc_set_loop_prop( 'elem', 'product' );
	wc_set_loop_prop( 'slider_nav', array( true, false, false ) );
	wc_set_loop_prop( 'slider_dot', array( false, true, true ) );
}

if ( $loop_changed ) {
	$product_style   = molla_option( 'post_product_type' );
	$product_align   = molla_option( 'product_align' );
	$product_hover   = molla_option( 'product_hover' );
	$show_op         = molla_option( 'product_show_op' );
	$wishlist_style  = molla_option( 'wishlist_style' );
	$out_stock_style = molla_option( 'out_stock_style' );
	$read_more       = molla_option( 'product_read_more' );
}

if ( ( molla_is_shop() || molla_is_in_category() ) && ! wc_get_loop_prop( 'widget' ) ) { // shop, category
	$layout_mode = molla_option( 'post_layout' );
	$shop_args   = molla_get_loop_columns();
	$cols        = $shop_args['columns'];
	if ( molla_is_shop() ) {
		$elem = get_option( 'woocommerce_shop_page_display' );
	} else {
		$elem = get_option( 'woocommerce_category_archive_display' );
	}
	if ( ! $elem || 'both' == $elem ) {
		$elem = 'product';
	} elseif ( 'subcategories' == $elem ) {
		$elem = 'product_cat';
	}
	$product_style = $shop_args['style'];
	if ( 'top' == $molla_settings['sidebar']['pos'] ) {
		$shop_sidebar_pos = 'top';
		if ( $layout_mode == 'grid' ) {
			$layout_mode_real = true;
		}
		$layout_mode = 'creative';
	}
	wc_set_loop_prop( 'columns', $cols );
	wc_set_loop_prop( 'cols_tablet', isset( $_COOKIE['device_width'] ) && $_COOKIE['device_width'] == 'md' ? $cols : 3 );
	wc_set_loop_prop( 'cols_mobile', isset( $_COOKIE['device_width'] ) && $_COOKIE['device_width'] == 'sm' ? $cols : 2 );
	wc_set_loop_prop( 'cols_under_mobile', isset( $_COOKIE['device_width'] ) && $_COOKIE['device_width'] == 'xs' ? $cols : 2 );
} else {
	$cols        = wc_get_loop_prop( 'columns' );
	$layout_mode = wc_get_loop_prop( 'layout_mode' );
	if ( wc_get_loop_prop( 'type' ) ) {
		$product_style   = wc_get_loop_prop( 'product_style' );
		$product_align   = wc_get_loop_prop( 'product_align' );
		$product_hover   = wc_get_loop_prop( 'product_hover' );
		$show_op         = wc_get_loop_prop( 'product_show_op' );
		$wishlist_style  = wc_get_loop_prop( 'wishlist_style' );
		$out_stock_style = wc_get_loop_prop( 'out_stock_style' );
		$read_more       = wc_get_loop_prop( 'product_read_more' );
	}
	$elem = wc_get_loop_prop( 'elem' );
}


if ( ! is_user_logged_in() && molla_option( 'catalog_mode' ) ) {
	$active_items = array();
	$show_public  = molla_option( 'public_product_show_op' );

	foreach ( $show_public as $op ) {
		if ( in_array( $op, $show_op ) ) {
			$active_items[] = $op;
		}
	}
	$show_op = $active_items;
}

$add_class .= esc_attr( $cols ) . ' ' . esc_attr( $layout_style );

if ( 'product' == $elem ) {
	$visible = array(
		'name'      => false,
		'cat'       => false,
		'tag'       => false,
		'price'     => false,
		'rating'    => false,
		'cart'      => false,
		'wishlist'  => false,
		'quickview' => false,
		'deal'      => false,
		'attribute' => false,
		'desc'      => false,
		'quantity'  => false,
	);

	foreach ( $visible as $key => $value ) {
		if ( in_array( $key, $show_op ) ) {
			$visible[ $key ] = true;
		}
	}

	$quickview_pos = -1;

	if ( $visible['quickview'] ) {
		if ( $loop_changed ) {
			$quickview_pos = molla_option( 'quickview_pos' );
		} else {
			$quickview_pos = wc_get_loop_prop( 'quickview_pos' );
		}
	}

	$wishlist_pos = -1;

	if ( $visible['wishlist'] ) {
		if ( $loop_changed ) {
			$wishlist_pos = molla_option( 'wishlist_pos' );
		} else {
			$wishlist_pos = wc_get_loop_prop( 'wishlist_pos' );
		}
	}

	// start variables for product layout for each product type
	$footer_action = $footer_out_body = $body_action = 0;

	if ( 'classic' == $product_style ||
		'simple' == $product_style ||
		'popup' == $product_style ||
		'card' == $product_style ) {
		$footer_action = 1;
	}

	if ( 'slide' == $product_style ||
		'no-overlay' == $product_style ) {
		$body_action = 1;
	}

	if ( 'popup' == $product_style ||
		'no-overlay' == $product_style ||
		'slide' == $product_style ) {
		$footer_out_body = 1;
	}

	$wishlist_footer = $quickview_footer = 0;

	if ( 'classic' == $product_style || 'card' == $product_style ) {
		$wishlist_footer = 1;
	}

	if ( $visible['quickview'] && ( 'popup' == $product_style || 'gallery-popup' == $product_style || 'card' == $product_style ) ) {
		if ( ! $quickview_pos ) {
			$quickview_pos = 'after-add-to-cart';
		}
		$quickview_footer = 1;
	}

	if ( 'list' == $product_style ) {
		$footer_action = 0;
		$body_action   = 0;
	}
	if ( wc_get_loop_prop( 'product_border' ) ) {
		$add_class .= ' split-line';
	}

	if ( 'list' == $product_style ) {
		if ( 'after-add-to-cart' == $quickview_pos ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'molla_quickview_html', 11 );
		}
		if ( 'after-add-to-cart' == $wishlist_pos ) {
			remove_action( 'woocommerce_after_shop_loop_item', 'molla_wishlist_html', 11 );
		} elseif ( 'after-product-title' == $wishlist_pos ) {
			add_action( 'molla_woocommerce_add_title_content', 'molla_wishlist_html', 10 );
		}
	} elseif ( 'widget' == $product_style ) {
		$visible['cart']      = false;
		$visible['wishlist']  = false;
		$visible['quickview'] = false;
		$visible['deal']      = false;
		$visible['attribute'] = false;
		$visible['desc']      = false;
		$body_action          = true;
		wc_set_loop_prop( 'product_labels', array() );
	} else {
		/**
		 * Hooks per product type
		 */
		// Card type
		if ( 'card' == $product_style ) {
			$visible['name'] = false;
			// Product Name
			add_action( 'woocommerce_before_shop_loop_item', 'molla_woocommerce_loop_product_title', 10 );
			remove_action( 'woocommerce_shop_loop_item_title', 'molla_woocommerce_loop_product_title', 10 );
			// Product Labels
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 25 );
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
			// Wrap price and rating
			add_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_loop_price_before', 5 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_loop_rating_after', 18 );
		} else {
			// Product Name
			remove_action( 'woocommerce_before_shop_loop_item', 'molla_woocommerce_loop_product_title', 10 );
			add_action( 'woocommerce_shop_loop_item_title', 'molla_woocommerce_loop_product_title', 10 );
			// Product Labels
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 25 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
			// Wrap price and rating
			remove_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_loop_price_before', 5 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_loop_rating_after', 18 );
		}

		// Actions' positions
		if ( 'after-add-to-cart' == $quickview_pos ) {
			add_action( 'woocommerce_after_shop_loop_item', 'molla_quickview_html', 11 );
		} else {
			remove_action( 'woocommerce_after_shop_loop_item', 'molla_quickview_html', 11 );
		}
		if ( 'after-add-to-cart' == $wishlist_pos ) {
			add_action( 'woocommerce_after_shop_loop_item', 'molla_wishlist_html', 11 );
		} else {
			remove_action( 'woocommerce_after_shop_loop_item', 'molla_wishlist_html', 11 );
		}
		if ( 'after-product-title' == $wishlist_pos ) {
			add_action( 'molla_woocommerce_add_title_content', 'molla_wishlist_html', 10 );
		} else {
			remove_action( 'molla_woocommerce_add_title_content', 'molla_wishlist_html', 10 );
		}
	}

	if ( $product_hover ) {
		add_action( 'woocommerce_before_shop_loop_item_title', 'molla_woocommerce_loop_product_image_hover', 11 );
	} else {
		remove_action( 'woocommerce_before_shop_loop_item_title', 'molla_woocommerce_loop_product_image_hover', 11 );
	}
	if ( $out_stock_style ) {
		add_action( 'woocommerce_after_shop_loop_item_title', 'molla_out_stock_html', 11 );
	} else {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'molla_out_stock_html', 11 );
	}

	if ( ! $visible['name'] ) {
		remove_action( 'woocommerce_shop_loop_item_title', 'molla_woocommerce_loop_product_title', 10 );
	} else {
		add_action( 'woocommerce_shop_loop_item_title', 'molla_woocommerce_loop_product_title', 10 );
	}

	if ( ! $visible['cat'] ) {
		remove_action( 'woocommerce_shop_loop_item_title', 'molla_woocommerce_shop_loop_category', 9 );
	} else {
		add_action( 'woocommerce_shop_loop_item_title', 'molla_woocommerce_shop_loop_category', 9 );
	}

	if ( ! $visible['tag'] ) {
		remove_action( 'woocommerce_shop_loop_item_title', 'molla_woocommerce_shop_loop_tag', 9 );
	} else {
		add_action( 'woocommerce_shop_loop_item_title', 'molla_woocommerce_shop_loop_tag', 9 );
	}

	if ( ! $visible['price'] ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	} else {
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	}

	if ( ! $visible['rating'] ) {
		remove_action( 'molla_woocommerce_loop_footer_content', 'woocommerce_template_loop_rating', 15 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );
	} else {
		if ( $footer_out_body ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );
			add_action( 'molla_woocommerce_loop_footer_content', 'woocommerce_template_loop_rating', 15 );
		} else {
			remove_action( 'molla_woocommerce_loop_footer_content', 'woocommerce_template_loop_rating', 15 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );
		}
	}

	if ( ! $visible['quantity'] ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_show_quantity', 31 );
	} else {
		wp_enqueue_script( 'bootstrap-input-spinner' );
		add_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_show_quantity', 31 );
	}

	if ( ! $visible['desc'] ) {
		remove_action( 'woocommerce_shop_loop_item_title', 'molla_product_short_description', 13 );
	} else {
		add_action( 'woocommerce_shop_loop_item_title', 'molla_product_short_description', 13 );
	}

	if ( ! $visible['cart'] ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
		if ( $read_more ) {
			add_action( 'woocommerce_after_shop_loop_item', 'molla_woocommerce_loop_read_more', 10 );
		} else {
			remove_action( 'woocommerce_after_shop_loop_item', 'molla_woocommerce_loop_read_more', 10 );
		}
	} else {
		remove_action( 'woocommerce_after_shop_loop_item', 'molla_woocommerce_loop_read_more', 10 );
		add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	}

	if ( ! $visible['attribute'] || 'list' == $product_style ) {
		remove_action( 'molla_woocommerce_loop_footer_content', 'molla_woocommerce_print_pickable_attrs', 20 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_print_pickable_attrs', 20 );
	} else {
		if ( $footer_out_body ) {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_print_pickable_attrs', 20 );
			add_action( 'molla_woocommerce_loop_footer_content', 'molla_woocommerce_print_pickable_attrs', 20 );
		} else {
			remove_action( 'molla_woocommerce_loop_footer_content', 'molla_woocommerce_print_pickable_attrs', 20 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'molla_woocommerce_print_pickable_attrs', 20 );
		}
	}

	add_filter( 'yith_wcwl_add_to_wishlist_button_classes', 'molla_add_to_wishlist_classes' );
	add_filter( 'molla_browse_wishlist_class', 'molla_add_to_wishlist_classes' );

	if ( class_exists( 'YITH_WCWL' ) && $visible['wishlist'] ) {
		if ( 'inner-thumbnail' == $wishlist_pos || ( ! $wishlist_footer && ! $wishlist_pos ) ) {
			$wishlist_pos = 'inner-thumbnail';
		} elseif ( ! $wishlist_pos && $wishlist_footer ) {
			$wishlist_pos = 'inner-body-footer';
		} elseif ( 'after-product-title' != $wishlist_pos ) {
			$wishlist_pos = -1;
		}
		if ( 'list' == $product_style ) {
			$wishlist_pos = 'inner-list';
		}
	} else {
		$wishlist_pos = -1;
	}
	if ( $visible['quickview'] ) {
		if ( 'center-thumbnail' == $quickview_pos ) {
		} elseif ( 'inner-thumbnail' == $quickview_pos || ( ! $quickview_footer && ! $quickview_pos ) ) {
			$quickview_pos = 'inner-thumbnail';
		} elseif ( ! $quickview_pos ) {
			$quickview_pos = 'inner-gallery';
		} elseif ( $quickview_footer && ! $quickview_pos ) {
			$quickview_pos = 'inner-footer';
		} else {
			$quickview_pos = -1;
		}
		if ( 'list' == $product_style ) {
			$quickview_pos = 'inner-list';
		}
	} else {
		$quickview_pos = -1;
	}

	wc_set_loop_prop( 'visible', $visible );
	wc_set_loop_prop( 'quickview_pos', $quickview_pos );
	wc_set_loop_prop( 'wishlist_pos', $wishlist_pos );
	wc_set_loop_prop( 'footer_action', $footer_action );
	wc_set_loop_prop( 'body_action', $body_action );
	wc_set_loop_prop( 'footer_out_body', $footer_out_body );
	// end variables for product layout for each product type


	if ( $loop_changed ) {
		wc_set_loop_prop( 'product_style', $product_style );
		wc_set_loop_prop( 'product_align', $product_align );
		wc_set_loop_prop( 'out_stock_style', molla_option( 'out_stock_style' ) );
		wc_set_loop_prop( 'product_vertical_animate', molla_option( 'product_vertical_animate' ) );
		wc_set_loop_prop( 'product_icon_hide', molla_option( 'product_icon_hide' ) );
		wc_set_loop_prop( 'product_label_hide', molla_option( 'product_label_hide' ) );
		wc_set_loop_prop( 'disable_product_out', molla_option( 'disable_product_out' ) );
		wc_set_loop_prop( 'action_icon_top', molla_option( 'action_icon_top' ) );
		wc_set_loop_prop( 'divider_type', molla_option( 'divider_type' ) );
		wc_set_loop_prop( 'product_label_type', molla_option( 'product_label_type' ) );
		wc_set_loop_prop( 'x_pos', 4 );
		wc_set_loop_prop( 'y_pos', 95 );
		wc_set_loop_prop( 't_y_pos', 'center' );
		wc_set_loop_prop( 'wishlist_style', $wishlist_style );
	}
}
$spacing = '' !== wc_get_loop_prop( 'spacing' ) ? (int) wc_get_loop_prop( 'spacing' ) : 20;

if ( 'creative' == $layout_mode ) {
	/*
	 * In case of general masonry layout
	 */
	wp_enqueue_script( 'isotope-pkgd' );
	$add_class             .= ' grid';
	$plugin_options_escaped = ' data-toggle="isotope"';
	if ( isset( $shop_sidebar_pos ) ) {

		$options = array(
			'layoutMode' => 'fitRows',
		);

		if ( ! isset( $layout_mode_real ) ) {
			$options['layoutMode'] = 'masonry';
		}
		$plugin_options_escaped .= ' data-isotope-options="' . esc_attr( json_encode( $options ) ) . '"';

	}
} elseif ( 'slider' == $layout_mode ) {
	wp_enqueue_script( 'owl-carousel' );

	$options           = array();
	$options['margin'] = $spacing;
	if ( wc_get_loop_prop( 'slider_autoheight' ) ) {
		$options['autoHeight'] = true;
	}
	$options['loop']     = wc_get_loop_prop( 'slider_loop' );
	$options['autoplay'] = wc_get_loop_prop( 'slider_auto_play' );
	$autoplaytime        = wc_get_loop_prop( 'slider_auto_play_time' );
	if ( $autoplaytime ) {
		$options['autoplayTimeout'] = $autoplaytime;
	}
	$options['center'] = wc_get_loop_prop( 'slider_center' );
	$slider_nav_show   = wc_get_loop_prop( 'slider_nav_show' );
	$add_class        .= ' owl-carousel owl-simple carousel-with-shadow' . ( 'yes' != $slider_nav_show ? ' owl-nav-show' : '' );
	if ( 'product' == $elem || 'product_cat' == $elem ) {
		$add_class .= wc_get_loop_prop( 'slider_nav_pos' ) ? ( ' ' . wc_get_loop_prop( 'slider_nav_pos' ) ) : '';
		$add_class .= wc_get_loop_prop( 'slider_nav_type' ) ? ( ' ' . wc_get_loop_prop( 'slider_nav_type' ) ) : '';
	}

	$args                  = array(
		0    => array(
			'items' => wc_get_loop_prop( 'cols_under_mobile' ),
			'dots'  => isset( wc_get_loop_prop( 'slider_dot' )[2] ) ? wc_get_loop_prop( 'slider_dot' )[2] : '',
			'nav'   => isset( wc_get_loop_prop( 'slider_nav' )[2] ) ? wc_get_loop_prop( 'slider_nav' )[2] : '',
		),
		576  => array(
			'items' => wc_get_loop_prop( 'cols_mobile' ),
			'dots'  => isset( wc_get_loop_prop( 'slider_dot' )[2] ) ? wc_get_loop_prop( 'slider_dot' )[2] : '',
			'nav'   => isset( wc_get_loop_prop( 'slider_nav' )[2] ) ? wc_get_loop_prop( 'slider_nav' )[2] : '',
		),
		768  => array(
			'items' => wc_get_loop_prop( 'cols_tablet' ),
			'dots'  => isset( wc_get_loop_prop( 'slider_dot' )[1] ) ? wc_get_loop_prop( 'slider_dot' )[1] : '',
			'nav'   => isset( wc_get_loop_prop( 'slider_nav' )[1] ) ? wc_get_loop_prop( 'slider_nav' )[1] : '',
		),
		992  => array(
			'items' => $cols,
			'dots'  => isset( wc_get_loop_prop( 'slider_dot' )[0] ) ? wc_get_loop_prop( 'slider_dot' )[0] : '',
			'nav'   => isset( wc_get_loop_prop( 'slider_nav' )[0] ) ? wc_get_loop_prop( 'slider_nav' )[0] : '',
		),
		1200 => array(
			'items' => wc_get_loop_prop( 'cols_upper_desktop' ),
			'dots'  => isset( wc_get_loop_prop( 'slider_dot' )[0] ) ? wc_get_loop_prop( 'slider_dot' )[0] : '',
			'nav'   => isset( wc_get_loop_prop( 'slider_nav' )[0] ) ? wc_get_loop_prop( 'slider_nav' )[0] : '',
		),
	);
	$options['responsive'] = molla_carousel_options( $args );
	$add_class            .= molla_carousel_responsive_classes( $options['responsive'] );

	$plugin_options_escaped = ' data-toggle="owl" data-owl-options="' . esc_attr( json_encode( $options ) ) . '"';
}

$loadmore = wc_get_loop_prop( 'load_more' );

if ( class_exists( 'WooCommerce' ) && molla_is_shop() || molla_is_in_category() ) {
	if ( ! wc_get_loop_prop( 'layout_mode' ) && 'creative' == $layout_mode && 'product_cat' == $elem && ! isset( $layout_mode_real ) ) {
		$creative_mode = molla_creative_grid_layout( molla_option( 'post_creative_layout' ) );
		if ( function_exists( 'molla_creative_grid_item_css' ) && $creative_mode ) {
			wc_set_loop_prop( 'is_custom_creative', true );
			molla_creative_grid_item_css( 0, $creative_mode, molla_option( 'post_creative_layout_height' ), molla_option( 'grid_gutter_width' ) );
		}
	}

	$loadmore = molla_option( 'shop_view_more_type' );
}

if ( 'list' != $product_style ) {
	if ( 'slider' != $layout_mode ) {
		$add_class .= ' row';

		if ( 'creative-grid' != wc_get_loop_prop( 'layout_mode' ) && ! wc_get_loop_prop( 'grid_type' ) && ( ! isset( $creative_mode ) || ! count( $creative_mode ) ) ) {
			$columns['xxl'] = $cols;
			$columns['xl']  = wc_get_loop_prop( 'cols_upper_desktop' );
			$columns['md']  = wc_get_loop_prop( 'cols_tablet' );
			$columns['sm']  = wc_get_loop_prop( 'cols_mobile' );
			$columns['xs']  = wc_get_loop_prop( 'cols_under_mobile' );

			if ( 'creative' == $layout_mode ) {
				$col_classes = array_slice( molla_get_column_class( $columns, true ), 2 );
				$col_classes = ' c-lg-' . $cols . ' ' . implode( ' ', $col_classes );
				$add_class  .= $col_classes;
			} else {
				$add_class .= molla_get_column_class( $columns );
			}
		}
	}
}

$add_class .= ' sp-' . $spacing;

if ( wc_get_loop_prop( 'is_custom_creative' ) ) {
	/*
	 * In case of preset masonry layout
	 */
	$plugin_options_escaped  = ' data-toggle="isotope"';
	$options                 = array(
		'masonry' => array(
			'columnWidth' => '.grid-space',
		),
	);
	$plugin_options_escaped .= ' data-isotope-options="' . esc_attr( json_encode( $options ) ) . '"';
}

if ( 'product' == $elem ) {
	$elem         = 'products ';
	$product_type = wc_get_loop_prop( 'product_style' ) ? wc_get_loop_prop( 'product_style' ) : molla_option( 'post_product_type' );
	$elem        .= 'products-' . $product_type . '-loop ';

	if ( 'scroll' == $loadmore ) {
		wp_enqueue_script( 'molla-infinite-scroll' );
		if ( $GLOBALS['woocommerce_loop']['current_page'] < $GLOBALS['woocommerce_loop']['total_pages'] ) {
			$elem .= 'infinite-scroll ';
		}
	}

	if ( 'creative-grid' == wc_get_loop_prop( 'layout_mode' ) ) {
		$add_class .= ' creative-grid';

		if ( function_exists( 'molla_is_elementor_preview' ) && molla_is_elementor_preview() ) {
			$add_class .= ' editor-mode';
		}
	}
} elseif ( 'product_cat' == $elem ) {
	$elem = 'product-categories ';
}

$main_query   = WC_Query::get_main_query();
$loop_options = $GLOBALS['woocommerce_loop'];
if ( $main_query ) {
	$query = $main_query->query;
	foreach ( $query as $var => $value ) {
		$loop_options['extra_atts'][ $var ] = $value;
	}
}

echo '<div class="' . apply_filters( 'molla_wc_shop_loop_classes', esc_attr( $elem ) . esc_attr( 'columns-' . esc_attr( $add_class ) ) ) . '" ' . $plugin_options_escaped . ' data-props="' . esc_attr( json_encode( $loop_options ) ) . '">';
