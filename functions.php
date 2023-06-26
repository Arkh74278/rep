<?php
/**
 * Woocommerce Hooked Functions
 */

if ( ! function_exists( 'molla_woocommerce_header_add_to_cart_fragment' ) ) :
	/**
	 * Refresh mini cart fragment.
	 */
	function molla_woocommerce_header_add_to_cart_fragment( $fragments ) {
		$_cart_total              = WC()->cart->get_cart_subtotal();
		$_cart_qty                = WC()->cart->cart_contents_count;
		$_cart_qty                = ( $_cart_qty > 0 ? $_cart_qty : '0' );
		$fragments['.cart-count'] = '<span class="cart-count">' . ( (int) $_cart_qty ) . '</span>';
		/* translators: %s: Cart items */
		$fragments['.cart-price'] = '<span class="cart-price">' . ( $_cart_total ) . '</span>';
		return $fragments;
	}
endif;

if ( ! function_exists( 'molla_woocommerce_mini_cart_item_name' ) ) :
	/**
	 * Get mini cart items' name.
	 */
	function molla_woocommerce_mini_cart_item_name( $name, $cart_item, $cart_item_key, $mini_cart = false ) {
		if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
			$first  = true;
			$link   = false;
			$canvas = molla_option( 'cart_canvas_type' );
			foreach ( $cart_item['variation'] as $attr_name => $value ) {
				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $attr_name ) ) );
				if ( taxonomy_exists( $taxonomy ) ) {
					// If this is a term slug, get the term's nice name.
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
				} else {
					// If this is a custom option slug, get the options name.
					$value = apply_filters( 'woocommerce_variation_option_name', $value, null, $taxonomy, $cart_item['data'] );
				}
				// Check the nicename against the title.
				if ( $value && ! wc_is_attribute_in_product_name( $value, $cart_item['data']->get_name() ) ) {
					if ( $canvas && $mini_cart ) {
						$name .= '</a><span class="variation">' . wc_attribute_label( $taxonomy ) . ': ' . $value . '</span>';
					} else {
						if ( $first ) {
							if ( false !== strpos( $name, '</a>' ) ) {
								$link = true;
								$name = str_replace( '</a>', '', $name );
							}
							$name .= ' - ' . $value;
							$first = false;
						} else {
							$name .= ', ' . $value;
						}
					}
				}
			}
			if ( $link ) {
				$name .= '</a>';
			}
		}
		$name = '<span>' . $name . '</span>';
		return $name;
	}
endif;

/**
 * Output Layout Html before Main content
 *
 */

if ( ! function_exists( 'molla_output_layout_before_main' ) ) {
	function molla_output_layout_before_main() {
		global $molla_settings;

		$page_width = $molla_settings['page']['width'];
		$sidebar    = $molla_settings['sidebar'];

		if ( molla_is_shop() || molla_is_in_category() ) {
			$sidebar_type = molla_option( 'shop_sidebar_type' );

			if ( ! molla_is_shop() && molla_is_in_category() ) {
				$term = get_queried_object();
				$meta = get_term_meta( $term->term_id, 'cat_sidebar', true );
				if ( 'default' != $meta ) {
					$sidebar_type = $meta;
				}
			}

			$sticky_sidebar = ( '' == $sidebar_type || 'top' == $sidebar['pos'] ? true : false ) && $sidebar['active'];

			echo '<div class="' . esc_attr( $page_width ) . esc_attr( $sidebar['active'] ? ( ' ' . $sidebar['pos'] . '-sidebar' ) : '' ) . ( $sticky_sidebar ? '' : ' toggle-sidebar' ) . ( ! woocommerce_product_loop() ? ' empty-archive' : '' ) . '">';
		} else {
			echo '<div class="' . esc_attr( $page_width ) . ( $sidebar['active'] ? ( ' ' . $sidebar['pos'] . '-sidebar' ) : '' ) . '">';
		}
	}
}

if ( ! function_exists( 'molla_woocommerce_item_data' ) ) :
	/**
	 * Avoid printing variations.
	 */
	function molla_woocommerce_item_data( $item_data, $cart_item ) {
		return array();
	}
endif;


if ( ! function_exists( 'molla_woocommerce_catalog_per_page' ) ) :
	/**
	 * Product archive count.
	 */
	function molla_woocommerce_catalog_per_page() {
		return molla_option( 'woocommerce_catalog_columns' );
	}
endif;

if ( ! function_exists( 'molla_woocommerce_shop_loop_category' ) ) :
	/**
	 * Product categories.
	 */
	function molla_woocommerce_shop_loop_category() {
		global $product;
		echo '<div class="product-cat">' . wc_get_product_category_list( $product->get_id(), ', ', '' ) . '</div>';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_shop_loop_tag' ) ) :
	/**
	 * Product tags.
	 */
	function molla_woocommerce_shop_loop_tag() {
		global $product;
		echo '<div class="product-cat">' . wc_get_product_tag_list( $product->get_id(), ', ', '' ) . '</div>';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_show_quantity' ) ) :
	/**
	 * Show quantity in product body.
	 */
	function molla_woocommerce_show_quantity() {
		global $product;
		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);
	}
endif;

if ( ! function_exists( 'molla_product_short_description' ) ) :
	/**
	 * Show quantity in product body.
	 */
	function molla_product_short_description() {
		global $product;
		$html  = '<div class="product-content">';
		$html .= apply_filters( 'molla_woocommerce_loop_content_excerpt', $product->get_short_description() );
		$html .= '</div>';
		echo molla_filter_output( $html );
	}
endif;


if ( ! function_exists( 'molla_woocommerce_loop_read_more' ) ) :
	/**
	 * Product loop action for visitors.
	 */
	function molla_woocommerce_loop_read_more() {
		echo '<a href="' . get_permalink() . '" class="btn-product"><span class="m-0">' . esc_html__( 'Read More', 'molla' ) . '</span></a>';
	}
endif;

if ( ! function_exists( 'molla_loop_product_thumbnail_size' ) ) :
	/**
	 * Product loop thumbnail size.
	 */
	function molla_loop_product_thumbnail_size() {
		if ( wc_get_loop_prop( 'widget' ) ) { // Product widget
			if ( isset( $GLOBALS['molla_loop_product_thumb_size'] ) ) {
				$size = $GLOBALS['molla_loop_product_thumb_size'];
				unset( $GLOBALS['molla_loop_product_thumb_size'] );
				return $size;
			}
			if ( wc_get_loop_prop( 'type' ) ) {
				return wc_get_loop_prop( 'thumbnail_size' ) ? wc_get_loop_prop( 'thumbnail_size' ) : 'woocommerce_thumbnail';
			}
		}
	}
endif;


if ( ! function_exists( 'molla_woocommerce_loop_product_image_hover' ) ) :
	/**
	 * Product hover image.
	 */
	function molla_woocommerce_loop_product_image_hover() {
		$id = get_the_ID();

		$image_size = molla_loop_product_thumbnail_size();

		$gallery          = get_post_meta( $id, '_product_image_gallery', true );
		$attachment_image = '';
		if ( ! empty( $gallery ) ) {
			$gallery          = explode( ',', $gallery );
			$first_image_id   = $gallery[0];
			$data             = array(
				'class' => 'product-image-hover',
			);
			$attachment_image = wp_get_attachment_image( $first_image_id, $image_size, false, $data );
			echo apply_filters( 'molla_product_hover_image_html', $attachment_image );
		}
	}
endif;

if ( ! function_exists( 'molla_woocommerce_loop_product_title' ) ) :
	/**
	 * Product loop title.
	 */
	function molla_woocommerce_loop_product_title() {

		$wishlist_pos  = wc_get_loop_prop( 'wishlist_pos' );
		$product_style = wc_get_loop_prop( 'product_style' );

		ob_start();
		if ( 'list' != $product_style && 'widget' != $product_style && 'after-product-title' == $wishlist_pos ) :
			?>
			<div class="product-title d-flex justify-content-between">
				<h3 class="<?php echo esc_attr( apply_filters( 'molla_woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title product-title' ) ); ?>"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo sanitize_text_field( get_the_title() ); ?></a></h3>
				<?php do_action( 'molla_woocommerce_add_title_content' ); ?>
			</div>
			<?php
		else :
			?>
			<h3 class="<?php echo esc_attr( apply_filters( 'molla_woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title product-title' ) ); ?>">
				<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo sanitize_text_field( get_the_title() ); ?></a>
				<?php do_action( 'molla_woocommerce_add_title_content' ); ?>
			</h3>
			<?php
		endif;
		echo ob_get_clean();
	}
endif;

if ( ! function_exists( 'molla_product_total_sales' ) ) {
	/**
	 * Product total salse
	 */
	function molla_product_total_sales() {
		$extra_atts = wc_get_loop_prop( 'extra_atts', '' );
		if ( $extra_atts && isset( $extra_atts['total_sales'] ) ) {

			$total_sales_mode = $extra_atts['total_sales'];
			if ( 'count' == $total_sales_mode || 'percent' == $total_sales_mode ) {
				global $product;
				$html = '';

				if ( 'percent' == $total_sales_mode ) {
					$html .= '<div class="product-sales-wrapper"><div class="product-sales-percent"></div></div>';
				}

				$html .= '<div class="product-sales">' . esc_html__( 'Sold:', 'molla' ) . ' ' . '<mark>' . esc_html( $product->get_total_sales() ) . '</mark></div>';

				echo apply_filters( 'molla_product_get_total_sales', $html );
			}
		}
	}
}


if ( ! function_exists( 'molla_loop_more_products' ) ) :
	/**
	 * Load more in shop.
	 */
	function molla_loop_more_products() {
		echo apply_filters( 'molla_loop_more_products_html', '<div class="more-container text-center"><a href="#" class="btn btn-more more-product">' . esc_html__( 'MORE PRODUCTS', 'molla' ) . '<i class="icon-refresh"></i></a></div>' );
	}
endif;

if ( ! function_exists( 'molla_get_star_rating_html' ) ) :
	/**
	 * Product reviews.
	 */
	function molla_get_star_rating_html( $html, $rating, $count ) {
		global $product;
			$count = ! $product ? 0 : $product->get_review_count();

		if ( ! $rating ) {
			$label = sprintf( esc_html__( 'Rated %s out of 5', 'molla' ), $rating );
			$html  = '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '"></div>';
		}
		if ( comments_open() ) {
			$is_single_product = ! molla_is_shop() && ! molla_is_in_category() && ! wc_get_loop_prop( 'elem' ) && ! isset( $_POST['quickview'] );
			$url               = esc_url( get_permalink() . '#reviews' );
			if ( $is_single_product ) { // Single Product Intro
				$url = '#reviews';
			}
			$review_link = '<a href="' . apply_filters( 'molla_star_rating_link', $url ) . '" class="woocommerce-review-link ratings-text" rel="nofollow">( ' . sprintf( _n( '%s Review', '%s Reviews', $count, 'molla' ), '<span class="count">' . esc_html( $count ) . '</span>' ) . ' )</a>';
			if ( ! $is_single_product && 'card' == wc_get_loop_prop( 'product_style' ) ) { // remove review link in card product type
				$review_link = '';
			}
			$html .= $review_link;
			$html  = '<div class="ratings-container">' . $html . '</div>';
		}
		return apply_filters( 'molla_star_rating_html', $html );
	}
endif;

if ( ! function_exists( 'molla_woocommerce_template_loop_category_title' ) ) :
	/**
	 * Product category class.
	 */
	function molla_woocommerce_template_loop_category_title( $category ) {
		?>
		<h2 class="woocommerce-loop-category__title cat-title">
			<?php
			echo '<a href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '">' . esc_html( $category->name ) . '</a>';

			echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">' . ( $category->count ? esc_html( $category->count ) : esc_html__( 'No', 'molla' ) ) . ( 1 == $category->count ? ' Product' : ' Products' ) . '</mark>', $category ); // WPCS: XSS ok.
			?>
		</h2>
		<?php
	}
endif;

if ( ! function_exists( 'molla_woocommerce_loop_price_before' ) ) :
	function molla_woocommerce_loop_price_before() {
		echo '<div class="price-rating-wrap">';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_loop_rating_after' ) ) :
	function molla_woocommerce_loop_rating_after() {
		echo '</div>';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_print_pickable_attrs' ) ) :
	/**
	 * Display attributes in listed products.
	 */
	function molla_woocommerce_print_pickable_attrs( $attributes = '', $pick_tax = false ) {
		global $product;
		$single = ( false !== $pick_tax );

		if ( false === $pick_tax ) { // from products widget or shop page
			$pick_tax = array();

			if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
				foreach ( $attribute_taxonomies as $tax ) {
					if ( ( 'pick' == $tax->attribute_type || ( isset( $tax->attribute_type_tmp ) && 'pick' == $tax->attribute_type_tmp ) ) && strpos( wc_attribute_taxonomy_name( $tax->attribute_name ), 'pa_' ) !== false ) {
						$pick_tax[] = wc_attribute_taxonomy_name( $tax->attribute_name );
					}
				}
			}
		}

		$show_attrs = molla_option( 'product_show_attrs' );

		if ( '' == $attributes ) {
			if ( 'variable' == $product->get_type() ) {
				$attributes = $product->get_variation_attributes();
			} else {
				$attributes = $product->get_attributes();
			}
		}
		foreach ( $attributes as $attribute_name => $options ) {
			if ( 'variable' != $product->get_type() ) {
				if ( is_object( $options ) ) {
					$options = $options['options'];
					for ( $i = 0; $i < count( $options ); $i++ ) {
						$t = get_term( $options[ $i ] );
						if ( $t ) {
							$options[ $i ] = $t->slug;
						}
					}
				} elseif ( ! is_array( $options ) ) {
					$options = array( $options );
				}
			}

			//if ( apply_filters( 'molla_check_product_variation_type', in_array( $attribute_name, $pick_tax ), $attribute_name, $options ) ) {
			if ( apply_filters( 'molla_check_product_variation_type', is_array( $show_attrs ) && in_array( $attribute_name, $show_attrs ), $attribute_name, $options ) ) {

				if ( in_array( $attribute_name, $pick_tax ) || ( ! $single ) ) {

					echo '<div class="nav-thumbs ' . $attribute_name . '">';

					$terms = wc_get_product_terms(
						$product->get_id(),
						$attribute_name,
						array(
							'fields' => 'all',
						)
					);

					if ( ! empty( apply_filters( 'molla_before_product_variation', $options, $attribute_name, $terms ) ) ) {
						if ( ! empty( $terms ) ) { // for existing terms
							foreach ( $terms as $term ) {
								if ( in_array( $term->slug, $options, true ) ) {
									$html  = '';
									$args  = '';
									$class = '';

									$attr_label = get_term_meta( $term->term_id, 'attr_label', true );
									$attr_color = get_term_meta( $term->term_id, 'attr_color', true );

									if ( in_array( $attribute_name, $pick_tax ) ) {
										if ( $attr_color ) { // Color pickable
											$class = 'nav-thumb thumb-color';
											$args  = ' style="background-color: ' . $attr_color . '"';
										} else { // Label Pickable ( like 'size' )
											$class = 'nav-thumb thumb-label';
											$html  = $attr_label ? $attr_label : $term->name;
										}

										printf( '<button class="%1$s" name="%2$s" %3$s>%4$s</button>', $class, $term->slug, $args, esc_html( $html ) );
									} elseif ( ! $single ) { // WC default selectable attribute ( e.x. author tag )
										$html = $term->name;
										$args = ' href="' . wc_get_page_permalink( 'shop' ) . '/?filter_' . substr( $attribute_name, 3 ) . '=' . $term->slug . '"';

										printf( '<a data-name="%1$s" %2$s>%3$s</a>', $term->slug, $args, esc_html( $html ) );
									}
								}
							}
						}
					}

					do_action( 'molla_after_product_variation', $options, $attribute_name, $terms );

					echo '</div>';
				}
			}
		}
	}
endif;



if ( ! function_exists( 'molla_woocommerce_template_loop_category_link' ) ) :
	/**
	 * Product category link.
	 */
	function molla_woocommerce_template_loop_category_link( $category ) {
		$class    = 'cat-link';
		$class    = apply_filters( 'molla_woocommerce_shop_link_classes', $class );
		$class   .= ' banner-link ' . wc_get_loop_prop( 'cat_btn_type', '' );
		$icon     = '';
		$icon_pos = wc_get_loop_prop( 'cat_btn_icon_pos' );
		$label    = wc_get_loop_prop( 'cat_btn_label' );
		$label    = $label ? $label : esc_html__( 'Shop Now', 'molla' );
		if ( wc_get_loop_prop( 'cat_btn_icon' ) ) {
			$icon = '<i class="' . wc_get_loop_prop( 'cat_btn_icon' ) . '"></i>';
		}
		if ( 'left' == $icon_pos ) {
			$class .= ' icon-before';
			$html   = $icon . apply_filters( 'molla_woocommerce_shop_link_html', $label );
		} else {
			$class .= ' icon-after';
			$html   = apply_filters( 'molla_woocommerce_shop_link_html', $label ) . $icon;
		}
		echo '<a href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '" class="' . esc_attr( $class ) . '">' . $html . '</a>';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_thumbnail_size' ) ) :
	/**
	 * Product category thumbnail.
	 */
	function molla_woocommerce_thumbnail_size( $image_size ) {
		return wc_get_loop_prop( 'image_size', $image_size );
	}
endif;

if ( ! function_exists( 'molla_woocommerce_product_subcategories' ) ) :
	function molla_woocommerce_product_subcategories( $category ) {
		if ( 'yes' == wc_get_loop_prop( 'with_subcat' ) ) {
			$terms = get_terms(
				'product_cat',
				array(
					'parent'     => $category->term_id, // $parent ),
					'hide_empty' => false,
					'number'     => wc_get_loop_prop( 'subcat_count', 3 ),
				)
			);
			if ( is_array( $terms ) ) {
				echo '<ul class="category-list">';
				$inner_html  = '';
				$subcat_icon = wc_get_loop_prop( 'subcat_icon' );
				if ( $subcat_icon ) {
					$inner_html = '<i class="' . $subcat_icon . '"></i>';
				}
				foreach ( $terms as $term ) {
					echo '<li><a href="' . get_term_link( $term ) . '">' . $inner_html . $term->name . '</a></li>';
					// $sub_ids .= $term->term_id . ',';
				}
				echo '</ul>';
			}
		}
	}
endif;

if ( ! function_exists( 'molla_woocommerce_result_count' ) ) :
	/**
	 * Product archive show option in toolbox.
	 */
	function molla_woocommerce_result_count() {
		if ( ( molla_is_shop() || molla_is_in_category() ) && '' == wc_get_loop_prop( 'load_more' ) && woocommerce_products_will_display() ) {
			echo '<div class="toolbox-center">';
			echo '<div class="toolbox-info">';
			woocommerce_result_count();
			echo '</div></div>';
		}
	}
endif;

if ( ! function_exists( 'molla_woocommerce_catalog_ordering' ) ) :
	/**
	 * Product archive ordering in toolbox.
	 */
	function molla_woocommerce_catalog_ordering() {
		if ( ( molla_is_shop() || molla_is_in_category() ) && '' == wc_get_loop_prop( 'load_more' ) && woocommerce_products_will_display() ) {
			echo '<div class="toolbox-right"><div class="toolbox-sort"><span class="label-sortby">' . esc_html__( 'Sort by:', 'molla' ) . '</span>';
			woocommerce_catalog_ordering();
			echo '</div>';
			molla_shop_grid_mods();
			echo '</div>';
		}
	}
endif;

if ( ! function_exists( 'molla_woocommerce_catalog_ordering_type' ) ) :
	/**
	 * Product archive ordering args in toolbox.
	 */
	function molla_woocommerce_catalog_ordering_type( $args ) {
		$args['menu_order'] = esc_html__( 'Default', 'molla' );
		$args['popularity'] = esc_html__( 'Popularity', 'molla' );
		$args['rating']     = esc_html__( 'Average Rating', 'molla' );
		$args['date']       = esc_html__( 'Newness', 'molla' );
		$args['price']      = esc_html__( 'Price: Low to High', 'molla' );
		$args['price-desc'] = esc_html__( 'Price: High to Low', 'molla' );
		$args['type']       = true;
		return $args;
	}
endif;

if ( ! function_exists( 'molla_woocommerce_cat_filter' ) ) :
	/**
	 * Product archive sidebar toggler.
	 */
	function molla_woocommerce_cat_filter() {
		$shop_sidebar_type = molla_option( 'shop_sidebar_type' );
		if ( ! molla_is_shop() && molla_is_in_category() ) {
			$meta = get_term_meta( get_queried_object()->term_id, 'cat_sidebar', true );
			if ( 'default' != $meta ) {
				$shop_sidebar_type = $meta;
			}
		}
		if ( molla_shop_content_is_cat() ) {
			return '<a href="#" class="sidebar-toggler filter-btn' . ( ! $shop_sidebar_type ? ' d-lg-none' : '' ) . '"><i class="icon-bars"></i>Filters</a>';
		}
		return '';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_filter_btn' ) ) :
	/**
	 * Product archive sidebar toggler in mobile.
	 */
	function molla_woocommerce_filter_btn() {
		global $molla_settings;
		if ( isset( $molla_settings['sidebar']['active'] ) && ! $molla_settings['sidebar']['active'] ) {
			return;
		}
		if ( ( molla_is_shop() || molla_is_in_category() ) && '' == wc_get_loop_prop( 'load_more' ) ) {
			$shop_sidebar_type = molla_option( 'shop_sidebar_type' );
			if ( ! molla_is_shop() && molla_is_in_category() ) {
				$meta = get_term_meta( get_queried_object()->term_id, 'cat_sidebar', true );
				if ( 'default' != $meta ) {
					$shop_sidebar_type = $meta;
				}
			}
			echo'<div class="toolbox-left' . ( ! $shop_sidebar_type ? ' d-lg-none' : '' ) . '"><a href="#" class="sidebar-toggler filter-btn"><i class="icon-bars"></i>Filters</a></div>';
		}
	}
endif;

if ( ! function_exists( 'molla_shop_grid_mods' ) ) :
	function molla_shop_grid_mods() {
		$ret               = '';
		$shop_col_args     = molla_get_loop_columns();
		$cols              = $shop_col_args['columns'];
		$post_product_type = $shop_col_args['style'];
		ob_start();
		?>
		<div class="toolbox-layout">
			<form class="grid-layout-link" method="get">
				<a href="#" class="btn-layout layout-list<?php echo 'list' == $post_product_type ? ' active' : ''; ?>">
					<svg width="16" height="10">
						<rect x="0" y="0" width="4" height="4"></rect>
						<rect x="6" y="0" width="10" height="4"></rect>
						<rect x="0" y="6" width="4" height="4"></rect>
						<rect x="6" y="6" width="10" height="4"></rect>
					</svg>
				</a>

				<a href="#" class="btn-layout layout-2col<?php echo ( 2 == intval( $cols ) && 'list' != $post_product_type ) ? ' active' : ''; ?>">
					<svg width="10" height="10">
						<rect x="0" y="0" width="4" height="4"></rect>
						<rect x="6" y="0" width="4" height="4"></rect>
						<rect x="0" y="6" width="4" height="4"></rect>
						<rect x="6" y="6" width="4" height="4"></rect>
					</svg>
				</a>

				<a href="#" class="btn-layout layout-3col<?php echo ( 3 == intval( $cols ) && 'list' != $post_product_type ) ? ' active' : ''; ?>">
					<svg width="16" height="10">
						<rect x="0" y="0" width="4" height="4"></rect>
						<rect x="6" y="0" width="4" height="4"></rect>
						<rect x="12" y="0" width="4" height="4"></rect>
						<rect x="0" y="6" width="4" height="4"></rect>
						<rect x="6" y="6" width="4" height="4"></rect>
						<rect x="12" y="6" width="4" height="4"></rect>
					</svg>
				</a>

				<a href="#" class="btn-layout layout-4col<?php echo ( 4 == intval( $cols ) && 'list' != $post_product_type ) ? ' active' : ''; ?>">
					<svg width="22" height="10">
						<rect x="0" y="0" width="4" height="4"></rect>
						<rect x="6" y="0" width="4" height="4"></rect>
						<rect x="12" y="0" width="4" height="4"></rect>
						<rect x="18" y="0" width="4" height="4"></rect>
						<rect x="0" y="6" width="4" height="4"></rect>
						<rect x="6" y="6" width="4" height="4"></rect>
						<rect x="12" y="6" width="4" height="4"></rect>
						<rect x="18" y="6" width="4" height="4"></rect>
					</svg>
				</a>
				<?php
				if ( ! empty( $_GET ) ) {
					foreach ( $_GET as $key => $val ) {
						if ( in_array( $key, array( 'layout_type', 'device_width', 'life_time' ) ) ) {
							continue;
						}
						echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
					}
				}
				?>
				<input type="hidden" class="layout-type" name="layout_type" value="">
				<input type="hidden" class="device-width" name="device_width" value="">
				<input type="hidden" class="life-time" name="life_time" value="yes">
			</form>
		</div>
		<?php
		echo ob_get_clean();
	}
endif;

// if ( ! function_exists( 'molla_wc_show_product_images' ) ) {
// 	function molla_wc_show_product_images() {
// 		woocommerce_show_product_images();
// 	}
// }

if ( ! function_exists( 'molla_wc_layered_nav_link' ) ) {
	function molla_wc_layered_nav_link( $link, $term = false ) {
		if ( ! isset( explode( '?', $link )[1] ) || molla_is_shop() || false != $term ) {
			return $link;
		}

		$queries     = explode( '&', explode( '?', $link )[1] );
		$curpage     = explode( '?', remove_query_arg( 'molla_vars' ) );
		$curargs     = isset( $curpage[1] ) ? $curpage[1] : '';
		$new_queries = '';
		foreach ( $queries as $key => $query ) {
			$ops = explode( '=', $query );
			$op1 = $ops[0];
			$op2 = isset( $ops[1] ) ? $ops[1] : '';

			if ( 'source_id' == $op1 ||
				'source_tax' == $op1 ||
				'post_type' == $op1 ||
				( 'product_cat' == $op1 && false === strpos( $curargs, 'product_cat=' ) ) ||
				( 'product_tag' == $op1 && false === strpos( $curargs, 'product_tag=' ) ) ) {
				continue;
			}

			$new_queries .= ( strlen( $new_queries ) ? '&' : '' ) . $op1 . '=' . $op2;
		}
		return $curpage[0] . ( $new_queries ? '?' . $new_queries : '' );
	}
}

if ( ! function_exists( 'molla_single_product_config_actions' ) ) :
	function molla_single_product_config_actions() {
		/**
		 * Woocommerce single product actions.
		 */
		$layout = molla_option( 'single_product_layout' );

		$layout_meta = get_post_meta( get_the_ID(), 'single_product_layout', true );

		if ( $layout_meta ) {
			if ( 'default' == $layout_meta ) {
				$layout = '';
			} else {
				$layout = $layout_meta;
			}
		}

		if ( 'sticky' == $layout || 'masonry_sticky' == $layout || 'full' == $layout ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs' );
			add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 70 );
		}
		add_filter( 'woocommerce_gallery_thumbnail_size', 'molla_single_product_thumbnail_size' );
		add_filter( 'woocommerce_gallery_image_size', 'molla_single_product_thumbnail_size' );
	}
endif;

if ( ! function_exists( 'molla_woocommerce_single_product_actions' ) ) :
	/**
	 * Single product init
	 */
	function molla_woocommerce_single_product_actions( $layout, $variable ) {

		$show_op = molla_option( 'public_product_show_op' );

		if ( ! in_array( 'cat', $show_op ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			remove_action( 'woocommerce_after_variations_form', 'woocommerce_template_single_meta', 40 );
			if ( 'gallery' == $layout ) {
				add_action(
					'woocommerce_after_variations_form',
					function() {
						do_action( 'woocommerce_share', 'Share:' );
					},
					40
				);
			} else {
				add_action(
					'woocommerce_single_product_summary',
					function() {
						do_action( 'woocommerce_share', 'Share:' );
					},
					40
				);
			}
		}
		if ( ! in_array( 'price', $show_op ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
		}
		if ( ! in_array( 'rating', $show_op ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
			remove_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_rating', 10 );
		}
		if ( ! in_array( 'cart', $show_op ) ) {
			if ( 'gallery' == $layout || $variable ) {
				remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
				remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
			}
			if ( 'gallery' != $layout || ! $variable ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			}
			add_filter(
				'molla_single_product_attrs_available',
				function() {
					return array();
				}
			);
		}
		if ( ! in_array( 'deal', $show_op ) ) {
			remove_action( 'woocommerce_single_product_summary', 'molla_woocommerce_single_product_deal', 25 );
			remove_action( 'woocommerce_before_variations_form', 'molla_woocommerce_single_product_deal', 25 );
		}
		if ( ! in_array( 'desc', $show_op ) ) {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			remove_action( 'woocommerce_before_variations_form', 'woocommerce_template_single_excerpt', 20 );
		}
	}
endif;

if ( ! function_exists( 'molla_single_product_wrapper_class' ) ) {
	/**
	 * Add classes to single product wrapper.
	 */
	function molla_single_product_wrapper_class( $args ) {
		$args[] = 'product-gallery';
		return $args;
	}
}

if ( ! function_exists( 'molla_attachment_image_params' ) ) :
	/**
	 * Single product image attrs.
	 */
	function molla_attachment_image_params( $args ) {
		if ( molla_option( 'lazy_load_img' ) ) {
			unset( $args['data-src'] );
		}
		return $args;
	}
endif;


if ( ! function_exists( 'molla_single_product_image_size' ) ) :
	/**
	 * Add data zoom-image for elevateZoom
	 */
	function molla_single_product_image_size( $ary, $id, $size, $main ) {
		$medium                 = wp_get_attachment_image_src( $id, 'medium' );
		$ary['data-image']      = isset( $medium[0] ) ? esc_url( $medium[0] ) : '';
		$full                   = wp_get_attachment_image_src( $id, 'full' );
		$ary['data-zoom-image'] = isset( $full[0] ) ? esc_url( $full[0] ) : '';
		return $ary;
	}
endif;

if ( ! function_exists( 'molla_single_product_thumbnail_size' ) ) :
	/**
	 * Change default single product image size as full.
	 */
	function molla_single_product_thumbnail_size( $arg ) {
		return 'full';
	}
endif;

if ( ! function_exists( 'molla_single_product_image_thumbnail_html' ) ) :
	/**
	 * Single product image html.
	 */
	function molla_single_product_image_thumbnail_html( $html, $id, $layout = -1, $widget_mode = '' ) {
		global $thumbnail_image;
		$quickview = isset( $_POST['quickview'] );
		if ( $widget_mode || $quickview ) {
			return $html;
		}
		if ( ! $thumbnail_image ) {
			$html    = substr( $html, 0, -6 );
			$actions = apply_filters( 'molla_single_product_actions', '<a href="#" class="sp-action btn-product-gallery"><i class="icon-arrows"></i></a>' );
			$html   .= '<div class="sp-actions-wrapper">' . $actions . '</div>';
			$html   .= '</div>';
		}
		return $html;
	}
endif;

/**
 * Molla Single Product - Gallery Image Functions
 */
if ( ! function_exists( 'molla_wc_get_gallery_image_html' ) ) {
	/**
	 * Get html of single product gallery image
	 *
	 * @since 1.0
	 * @param int $attachment_id        Image ID
	 * @param boolean $main_image       True if large image is needed
	 * @param boolean $featured_image   True if attachment is featured image
	 * @param boolean $is_thumbnail     True if thumb wrapper is needed
	 * @return string image html
	 */
	function molla_wc_get_gallery_image_html( $attachment_id, $main_image = false, $featured_image = false, $is_thumbnail = true ) {

		if ( $main_image ) {
			// Get large image

			$image_size    = apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single' );
			$full_size     = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
			$thumbnail_src = wp_get_attachment_image_src( $attachment_id, 'woocommerce_single' );
			$full_src      = wp_get_attachment_image_src( $attachment_id, $full_size );
			$alt_text      = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
			$image         = wp_get_attachment_image(
				$attachment_id,
				$image_size,
				false,
				apply_filters(
					'woocommerce_gallery_image_html_attachment_image_params',
					array(
						'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-src'                => esc_url( ! empty( $full_src ) ? $full_src[0] : '' ),
						'data-large_image'        => esc_url( ! empty( $full_src[0] ) ? $full_src[0] : '' ),
						'data-large_image_width'  => ! empty( $full_src[1] ) ? $full_src[1] : '',
						'data-large_image_height' => ! empty( $full_src[2] ) ? $full_src[2] : '',
						'class'                   => $featured_image ? 'wp-post-image' : '',
					),
					$attachment_id,
					$image_size,
					$main_image
				)
			);

			if ( $is_thumbnail ) {
				$image = '<div data-thumb="' . esc_url( ! empty( $thumbnail_src[0] ) ? $thumbnail_src[0] : '' ) . ( $alt_text ? '" data-thumb-alt="' . esc_attr( $alt_text ) : '' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( ! empty( $full_src[0] ) ? $full_src[0] : '' ) . '">' . $image . '</a></div>';
			}
		} else {
			// Get small image

			$thumbnail_size = apply_filters( 'molla_wc_thumbnail_image_size', 'woocommerce_thumbnail' );
			//	var_dump( $thumbnail_size );

			if ( $attachment_id ) {
				// If default or horizontal layout, print simple image tag
				$gallery_thumbnail = false;
				$image_sizes       = wp_get_additional_image_sizes();
				if ( isset( $image_sizes[ $thumbnail_size ] ) ) {
					$gallery_thumbnail = $image_sizes[ $thumbnail_size ];
				}
				if ( ! $gallery_thumbnail ) {
					$gallery_thumbnail = wc_get_image_size( $thumbnail_size );
				}
				//		var_dump( $gallery_thumbnail);

				if ( 0 == $gallery_thumbnail['height'] ) {
					$full_image_size = wp_get_attachment_image_src( $attachment_id, 'full' );
					if ( isset( $full_image_size[1] ) && $full_image_size[1] ) {
						$gallery_thumbnail['height'] = intval( $gallery_thumbnail['width'] / absint( $full_image_size[1] ) * absint( $full_image_size[2] ) );
					}
				}

				$thumbnail_size = apply_filters( 'molla_woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
				$image_src      = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
				$image          = '<img alt="' . esc_attr( _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ) ) . '" src="' . esc_url( ! empty( $image_src[0] ) ? $image_src[0] : '' ) . '" width="' . (int) ( ! empty( $thumbnail_size[0] ) ? $thumbnail_size[0] : '' ) . '" height="' . (int) ( ! empty( $thumbnail_size[1] ) ? $thumbnail_size[1] : '' ) . '">';

			} else {
				$image = '';
			}

			if ( $is_thumbnail && $image ) {
				$image = '<div class="product-thumb"><a href="' . esc_url( ! empty( $image_src[0] ) ? $image_src[0] : '' ) . '" class="' . ( $featured_image ? ' active' : '' ) . '">' . $image . '</a></div>';
			}
		}
		return apply_filters( 'molla_wc_get_gallery_image_html', $image );
	}
}

// Product ( Gallery Type ) layout
if ( ! function_exists( 'molla_single_product_1st_wrap_start' ) ) :
	function molla_single_product_1st_wrap_start() {
		echo '<div class="col-md-6">';
	}
endif;
if ( ! function_exists( 'molla_single_product_1st_end_2nd_start' ) ) :
	function molla_single_product_1st_end_2nd_start() {
		echo '</div>';
		echo '<div class="col-md-6">';
	}
endif;
if ( ! function_exists( 'molla_single_product_2nd_wrap_end' ) ) :
	function molla_single_product_2nd_wrap_end() {
		echo '</div>';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_single_product_deal' ) ) :
	/**
	 * Single product summary.
	 */
	function molla_woocommerce_single_product_deal() {
		global $product;
		if ( $product->is_on_sale() ) {
			$limit = molla_option( 'single_product_limit_time' );
			if ( molla_is_product() && $product->is_type( 'variable' ) ) {
				$variations = $product->get_available_variations();
				$end_date   = '';
				$sale_date  = '';
				foreach ( $variations as $variation ) {
					$new_date = get_post_meta( $variation['variation_id'], '_sale_price_dates_to', true );
					if ( ! $new_date || ( $end_date && $end_date != $new_date ) ) {
						$end_date = false;
					} elseif ( $new_date ) {
						if ( false !== $end_date ) {
							$end_date = $new_date;
						}
						$sale_date = $new_date;
					}
					if ( false === $end_date && $sale_date ) {
						break;
					}
				}
				if ( $end_date ) {
					$end_date = date( 'Y/m/d H:i:s', (int) $end_date );
				} elseif ( $sale_date ) {
					$end_date = date( 'Y/m/d H:i:s', (int) $sale_date );
				}
			} else {
				$end_date = $product->get_date_on_sale_to();
				if ( $end_date ) {
					$end_date = $product->get_date_on_sale_to()->date( 'Y/m/d H:i:s' );
				}
			}

			$end_date   = strtotime( $end_date );
			$now        = strtotime( 'now' );
			$prefix     = '';
			$time_limit = (int) $limit * 86400;

			if ( $end_date ) {
				$offset = $end_date - $now;

				$type       = apply_filters( 'molla_product_countdown_type', molla_option( 'product_deal_type' ) );
				$label      = apply_filters( 'molla_product_countdown_label', molla_option( 'product_deal_label' ) );
				$bool_short = molla_option( 'product_deal_section_period' );

				if ( apply_filters( 'molla_is_single_product_widget', false ) ) {
					$prefix = '<div class="deal-prefix"><i class="far fa-clock"></i><span>' . esc_html__( 'Hurry Up! ', 'molla' ) . '</span>';
					if ( 'inline' != $type ) {
						$prefix .= '<span>' . esc_html__( 'Offer end in', 'molla' ) . '</span>';
					}
					$prefix .= '</div>';
				}

				$meta = get_post_meta( get_the_ID(), 'product_deal_type' );

				if ( $meta ) {
					$type = $meta[0];
				}

				echo '<div class="deal-container ' . $type . '-type">' . ( $time_limit > $offset && ! strlen( $GLOBALS['woocommerce_loop']['name'] ) ? apply_filters( 'molla_product_countdown_prefix', $prefix ) : '' ) . ( 'inline' == $type && $label ? ( '<span class="countdown-title">' . esc_html( $label ) . '</span>' ) : '' ) . '<div class="deal-countdown" data-until=' . esc_attr( $offset ) . ' data-relative="true" ' . ( $bool_short ? 'data-labels-short="true"' : '' ) . '></div></div>';
			}
		}
	}
endif;

if ( ! function_exists( 'molla_add_woocommerce_quantity_label' ) ) :
	/**
	 * Single product summary quantity.
	 */
	function molla_add_woocommerce_quantity_label() {
		$label = apply_filters( 'molla_woocommerce_quantity_label', esc_html__( 'Qty', 'molla' ) );
		echo '<label>' . $label . ':</label>';
	}
endif;

if ( ! function_exists( 'molla_variation_dropdown_args' ) ) :
	/**
	 * Single product attribute options.
	 */
	function molla_variation_dropdown_args( $arg ) {
		$arg['class']            = 'form-control';
		$attr                    = str_replace( 'pa_', '', $arg['attribute'] );
		$attr                    = esc_html__( 'Select a ', 'molla' ) . $attr;
		$arg['show_option_none'] = $attr;
		return $arg;
	}
endif;

if ( ! function_exists( 'molla_variation_dropdown_html' ) ) :
	/**
	 * Single product attribute options html.
	 */
	function molla_variation_dropdown_html( $html, $args ) {
		$attr      = str_replace( 'Select a ', '', $args['show_option_none'] );
		$link_size = '';
		if ( 'size' == $attr ) {
			$guide    = false;
			$size_tab = get_post_meta( get_the_ID(), 'size_tab_name', true );
			if ( 'global' != $size_tab && $size_tab ) {
				if ( get_post_meta( get_the_ID(), 'tab_title_' . $size_tab, true ) ) {
					$guide = true;
				}
			} else {
				if ( molla_option( 'single_product_tab_title' ) ) {
					$guide = true;
				}
			}
			if ( $guide && ! isset( $_POST['quickview'] ) ) {
				$link_size .= '<a href="#" class="size-guide"><i class="icon-th-list"></i>' . esc_html__( 'size guide', 'molla' ) . '</a>';
			}
		}
		return '<div class="select-custom">' . $html . '</div>' . $link_size;
	}
endif;

if ( ! function_exists( 'molla_woocommerce_display_product_attrs' ) ) :
	/**
	 * Product attributes html.
	 */
	function molla_woocommerce_display_product_attrs( $attrs, $product ) {
		$ret = array();
		foreach ( $attrs as $attr ) {
			$label = $attr['label'];
			if ( false === strpos( $attr['value'], '<p>' ) ) {
				$value = '<p>' . $attr['value'] . '</p>';
			} else {
				$value = $attr['value'];
			}
			$ret[] = array(
				'label' => $label,
				'value' => $value,
			);
		}

		return $ret;
	}
endif;

if ( ! function_exists( 'molla_woocommerce_product_meta_wrap_start' ) ) :
	/**
	 * Single product meta wrap.
	 */
	function molla_woocommerce_product_meta_wrap_start() {
		echo '<div class="product-meta-wrap">';
	}
endif;
if ( ! function_exists( 'molla_woocommerce_product_meta_wrap_end' ) ) :
	/**
	 * Single product meta wrap.
	 */
	function molla_woocommerce_product_meta_wrap_end() {
		echo '</div>';
		do_action( 'woocommerce_share', 'Share:' );
	}
endif;

if ( ! function_exists( 'molla_sticky_add_to_cart_start' ) ) :
	/**
	 * Single product add-to-cart.
	 */
	function molla_sticky_add_to_cart_start() {
		if ( apply_filters( 'molla_sticky_add_to_cart_start', ( ! molla_is_product() || ! molla_option( 'single_sticky_bar_show' ) ) ) ) {
			return;
		}

		global $product, $molla_settings;
		echo '<div class="sticky-bar-wrapper">';
		echo '<div class="sticky-bar">';
		echo '<div class="' . $molla_settings['page']['width'] . '">';
		echo '<div class="sticky-bar-product">';
		$image_id = $product->get_image_id();
		$image    = wp_get_attachment_image_src( $image_id, 'woocommerce_gallery_thumbnail' );
		if ( $image ) {
			$image = '<img src="' . $image[0] . '" class="sticky-add-to-cart-img" />';
			echo molla_strip_script_tags( $image );
		}
		echo '<div class="product-title">' . sanitize_text_field( get_the_title() ) . '</div>';
		echo '</div>';
		echo '<div class="sticky-bar-action">';
		if ( ! $product->is_type( 'variable' ) ) {
			woocommerce_template_single_price();
		}
	}
endif;

if ( ! function_exists( 'molla_sticky_add_to_cart_end' ) ) :
	/**
	 * Single product add-to-cart.
	 */
	function molla_sticky_add_to_cart_end() {
		if ( apply_filters( 'molla_sticky_add_to_cart_end', ( ! molla_is_product() || ! molla_option( 'single_sticky_bar_show' ) ) ) ) {
			return;
		}

		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
endif;

if ( ! function_exists( 'molla_product_description_heading' ) ) :
	/**
	 * Change single product description tab heading
	 */
	function molla_product_description_heading( $heading ) {
		$heading = esc_html__( 'Product Information', 'molla' );
		return $heading;
	}
endif;

if ( ! function_exists( 'molla_product_additional_info_heading' ) ) :
	/**
	 * Change single product additional info heading
	 */
	function molla_product_additional_info_heading( $heading ) {
		$heading = esc_html__( 'Information', 'molla' );
		return $heading;
	}
endif;

if ( ! function_exists( 'molla_woocommerce_reviews_title' ) ) :
	/**
	 * Change single product review heading
	 */
	function molla_woocommerce_reviews_title( $title, $count, $product ) {
		$title = esc_html__( 'Reviews', 'molla' ) . ' (' . $count . ')';
		return $title;
	}
endif;

if ( ! function_exists( 'molla_review_author' ) ) :
	function molla_review_author() {
		?>
		<h4 class="woocommerce-review__author"><?php comment_author(); ?></h4>
		<?php
	}
endif;

if ( ! function_exists( 'molla_single_product_next_prev_nav' ) ) :
	/**
	 * Add product prev, next navigation
	 */
	function molla_single_product_next_prev_nav() {

		if ( apply_filters( 'molla_check_single_next_prev_nav', molla_is_product() ) ) :
			global $post;

			$next_post = get_next_post( true, '', 'product_cat' );
			$prev_post = get_previous_post( true, '', 'product_cat' );

			$next_label_escaped = apply_filters( 'molla_woocommerce_next_html', esc_html__( 'Next', 'molla' ) );
			$prev_label_escaped = apply_filters( 'molla_woocommerce_prev_html', esc_html__( 'Prev', 'molla' ) );

			ob_start();

			?>
			<ul class="product-pager ml-auto">
				<?php
				if ( is_a( $prev_post, 'WP_Post' ) ) :
					?>
					<li class="product-pager-link">
						<a class="product-pager-prev" href="<?php echo esc_url( get_the_permalink( $prev_post->ID ) ); ?>" aria-label="Previous" tabindex="-1">
							<i class="<?php echo apply_filters( 'molla_woocommerce_prev_icon', 'icon-angle-' . ( is_rtl() ? 'right' : 'left' ) ); ?>"></i>
							<span><?php echo molla_filter_output( $prev_label_escaped ); ?></span>
						</a>
						<div class="dropdown product-thumbnail">
							<a title="<?php echo sanitize_text_field( get_the_title( $prev_post->ID ) ); ?>" href="<?php echo esc_url( get_the_permalink( $prev_post->ID ) ); ?>"><?php echo get_the_post_thumbnail( $prev_post->ID, apply_filters( 'single_product_small_thumbnail_size', 'thumbnail' ) ) . '<h3 class="product-title">' . esc_html( get_the_title( $prev_post->ID ) ) . '</h3>'; ?></a>
						</div>
					</li>
					<?php
				endif;

				if ( is_a( $next_post, 'WP_Post' ) ) :
					?>
					<li class="product-pager-link">
						<a class="product-pager-next" href="<?php echo esc_url( get_the_permalink( $next_post->ID ) ); ?>" aria-label="Next" tabindex="-1">
							<span><?php echo molla_filter_output( $next_label_escaped ); ?></span>
							<i class="<?php echo apply_filters( 'molla_woocommerce_next_icon', 'icon-angle-' . ( is_rtl() ? 'left' : 'right' ) ); ?>"></i>
						</a>
						<div class="dropdown product-thumbnail">
							<a title="<?php echo sanitize_text_field( get_the_title( $next_post->ID ) ); ?>" href="<?php echo esc_url( get_the_permalink( $next_post->ID ) ); ?>"><?php echo get_the_post_thumbnail( $next_post->ID, apply_filters( 'single_product_small_thumbnail_size', 'thumbnail' ) ) . '<h3 class="product-title">' . esc_html( get_the_title( $next_post->ID ) ) . '</h3>'; ?></a>
						</div>
					</li>
					<?php
				endif;
				?>
			</ul>
			<?php

			return apply_filters( 'molla_single_product_next_prev_nav', ob_get_clean() );
		endif;
	}
endif;

if ( ! function_exists( 'molla_single_product_stock_param' ) ) :
	function molla_single_product_stock_param( $params, $product ) {

		if ( ! $product->is_in_stock() ) {
		} elseif ( $product->managing_stock() && $product->is_on_backorder( 1 ) ) {
		} elseif ( ! $product->managing_stock() && $product->is_on_backorder( 1 ) ) {
		} elseif ( $product->managing_stock() ) {
			$display      = __( 'In stock', 'molla' );
			$stock_amount = $product->get_stock_quantity();

			if ( molla_product_is_in_low_stock( $product ) ) {
				/* translators: %s: stock amount */
				$display = sprintf( __( 'Hurry! Only <strong>%s</strong> left in stock', 'molla' ), wc_format_stock_quantity_for_display( $stock_amount, $product ) );

			} else {
				/* translators: %s: stock amount */
				$display = sprintf( __( '<strong>%s</strong> in stock', 'molla' ), wc_format_stock_quantity_for_display( $stock_amount, $product ) );
			}

			if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
				$display .= ' ' . __( '(can be backordered)', 'molla' );
			}

			$params['availability'] = $display;
		}

		return $params;
	}
endif;

if ( ! function_exists( 'molla_review_like_count' ) ) :
	function molla_review_like_count( $id ) {
		$count = get_post_meta( $id, 'like_count', true ) ? get_post_meta( $id, 'like_count', true ) : 0;
		return  $count;
	}
endif;

if ( ! function_exists( 'molla_review_dislike_count' ) ) :
	function molla_review_dislike_count( $id ) {
		$count = get_post_meta( $id, 'dislike_count', true ) ? get_post_meta( $id, 'dislike_count', true ) : 0;
		return $count;
	}
endif;

// single product custom tabs
if ( ! function_exists( 'molla_woocommerce_product_custom_tabs' ) ) :
	function molla_woocommerce_product_custom_tabs( $array ) {
		$global = molla_option( 'single_product_tab_title' );
		if ( $global ) {
			$array['global'] = array(
				'title'    => sanitize_text_field( $global ),
				'priority' => 40,
				'callback' => 'molla_woocommerce_product_print_custom_tab',
			);
		}
		$meta_title = get_post_meta( get_the_ID(), 'tab_title_block', true );
		if ( $meta_title ) {
			$array['block'] = array(
				'title'    => sanitize_text_field( $meta_title ),
				'priority' => 50,
				'callback' => 'molla_woocommerce_product_print_custom_tab',
			);
		}
		$meta_title = get_post_meta( get_the_ID(), 'tab_title_1st', true );
		if ( $meta_title ) {
			$array['1st'] = array(
				'title'    => sanitize_text_field( $meta_title ),
				'priority' => 60,
				'callback' => 'molla_woocommerce_product_print_custom_tab',
			);
		}
		$meta_title = get_post_meta( get_the_ID(), 'tab_title_2nd', true );
		if ( $meta_title ) {
			$array['2nd'] = array(
				'title'    => sanitize_text_field( $meta_title ),
				'priority' => 70,
				'callback' => 'molla_woocommerce_product_print_custom_tab',
			);
		}
		return $array;
	}
endif;
if ( ! function_exists( 'molla_woocommerce_product_print_custom_tab' ) ) :
	function molla_woocommerce_product_print_custom_tab( $key, $product_tab ) {
		wc_get_template( 'single-product/tabs/custom_tab.php', array( 'tab_name' => $key ) );
	}
endif;


if ( ! function_exists( 'molla_woocommerce_shipping_label' ) ) :
	/**
	 * Cart shipping label.
	 */
	function molla_woocommerce_shipping_label() {
		echo '<label>' . __( 'Estimate for Your Country', 'molla' ) . '</label>';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_checkout_fields' ) ) {
	/**
	 * Customize checkout fields
	 */
	function molla_woocommerce_checkout_fields( $fields ) {
		$fields['billing']['billing_city']['class']       = array(
			'form-row-first',
			'address-field',
		);
		$fields['billing']['billing_state']['class']      = array(
			'form-row-first',
			'address-field',
		);
		$fields['billing']['billing_phone']['class']      = array(
			'form-row-first',
			'address-field',
		);
		$fields['billing']['billing_postcode']['class']   = array(
			'form-row-first',
			'address-field',
		);
		$fields['shipping']['shipping_city']['class']     = array(
			'form-row-first',
			'address-field',
		);
		$fields['shipping']['shipping_state']['class']    = array(
			'form-row-first',
			'address-field',
		);
		$fields['shipping']['shipping_postcode']['class'] = array(
			'form-row-first',
			'address-field',
		);

		return $fields;
	}
}

function molla_add_action_to_my_account() {
	/**
	 * My account page init
	 */
	if ( is_page( wc_get_page_id( 'myaccount' ) ) ) :
		add_filter( 'molla_page_content_class', 'molla_account_page_class' );
		add_filter(
			'molla_page_content_attrs',
			function() {
				$ret = '';
				if ( molla_option( 'lazy_load_img' ) ) {
					$img_url = ! empty( molla_option( 'woo_account_background' )['background-image'] ) ? molla_option( 'woo_account_background' )['background-image'] : '';
					if ( ! $img_url ) {
						$img_url = MOLLA_URI . '/assets/images/login-bg.jpg';
					}
					$ret = ' data-src=' . esc_url( $img_url );
				}
				return $ret;
			}
		);
	endif;
}

if ( ! function_exists( 'molla_account_page_class' ) ) :
	function molla_account_page_class() {
		$ret = 'myaccount-content';
		if ( ! is_user_logged_in() ) {
			$ret .= ' logged-out';
			if ( molla_option( 'lazy_load_img' ) && molla_option( 'woo_account_background' ) ) {
				$ret .= ' molla-lazyload-back';
			}
		}
		return $ret;
	}
endif;

if ( ! function_exists( 'molla_woocommerce_pass_form_wrap_before' ) ) :
	/**
	 * My account form box wrap start
	 */
	function molla_woocommerce_pass_form_wrap_before() {
		echo '<div class="form-box pt-12 pb-12">';
	}
endif;

if ( ! function_exists( 'molla_woocommerce_pass_form_wrap_after' ) ) :
	/**
	 * My account form box wrap end
	 */
	function molla_woocommerce_pass_form_wrap_after() {
		echo '</div>';
	}
endif;

if ( ! function_exists( 'molla_dashboard_nav_wrap_start' ) ) :
	function molla_dashboard_nav_wrap_start() {
		/**
		 * Dashboard navigation wrap start
		 */
		echo '<div class="row dashboard-wrap"><div class="col-md-4 col-lg-3">';
	}
endif;

if ( ! function_exists( 'molla_dashboard_nav_wrap_end' ) ) :
	function molla_dashboard_nav_wrap_end() {
		/**
		 * Dashboard navigation wrap end
		 */
		echo '</div>';
	}
endif;

if ( ! function_exists( 'molla_dashboard_content_wrap_start' ) ) :
	function molla_dashboard_content_wrap_start() {
		/**
		 * Dashboard content wrap start
		 */
		echo '<div class="col-md-8 col-lg-9">';
	}
endif;

if ( ! function_exists( 'molla_dashboard_content_wrap_end' ) ) :
	function molla_dashboard_content_wrap_end() {
		/**
		 * Dashboard content wrap end
		 */
		echo '</div></div>';
	}
endif;


if ( ! function_exists( 'molla_account_privacy_policy_text' ) ) :
	/**
	 * Account Policy
	 */
	function molla_account_privacy_policy_text( $text = null, $type = null ) {
		return $text;
	}
endif;

if ( ! function_exists( 'molla_breadcrumb_action' ) ) :
	/**
	 * Woocommerce breadcrumb.
	 */
	function molla_breadcrumb_action() {
		global $post;

		$breadcrumb_active = molla_option( 'breadcrumb_show' );
		if ( $post ) {
			$post_id = molla_get_page_layout( $post );

			$active = get_post_meta( $post_id, 'breadcrumb' );
			if ( $active ) {
				if ( 'show' == $active[0] ) {
					$breadcrumb_active = true;
				} else {
					$breadcrumb_active = false;
				}
			}
		}
		if ( $breadcrumb_active ||
			molla_is_product() ||
			molla_shop_content_is_cat() ) {

			add_action( 'page_content_before', 'molla_woocommerce_breadcrumb' );
		}
		if ( ! $breadcrumb_active && ( molla_is_product() || molla_shop_content_is_cat() ) ) {
			add_filter( 'woocommerce_get_breadcrumb', 'molla_print_breadcrumb_content', 10, 2 );
		}
	}
endif;
if ( ! function_exists( 'molla_woocommerce_breadcrumb' ) ) :
	function molla_woocommerce_breadcrumb() {
		woocommerce_breadcrumb();
	}
endif;
if ( ! function_exists( 'molla_print_breadcrumb_content' ) ) :
	function molla_print_breadcrumb_content( $crumbs, $obj ) {
		$crumbs = array();
		return $crumbs;
	}
endif;
if ( ! function_exists( 'molla_woocommerce_breadcrumb_args' ) ) :
	function molla_woocommerce_breadcrumb_args( $args ) {

		$width = molla_option( 'breadcrumb_width' );
		if ( ! $width ) {
			global $molla_settings;
			$width = $molla_settings['page']['width'];
		}
		$divider_active = molla_option( 'breadcrumb_divider_active' );
		$divider_width  = molla_option( 'breadcrumb_divider_width' );

		global $post;
		$post_id = molla_get_page_layout( $post );

		$breadcrumb          = get_post_meta( $post_id, 'breadcrumb' );
		$meta_width          = get_post_meta( $post_id, 'breadcrumb_width' );
		$meta_divider_active = get_post_meta( $post_id, 'breadcrumb_divider' );
		$meta_divider_width  = get_post_meta( $post_id, 'breadcrumb_divider_width' );

		if ( $breadcrumb && 'show' == $breadcrumb[0] ) {
			if ( $meta_width ) {
				$meta_width = $meta_width[0];
				if ( 'page-width' != $meta_width ) {
					$width = $meta_width;
				}
			} else {
				$thm_width = molla_option( 'breadcrumb_width' );
				if ( $thm_width ) {
					$width = $thm_width;
				}
			}

			if ( $meta_divider_active ) {
				if ( 'show' == $meta_divider_active[0] ) {
					$divider_active = true;
				} else {
					$divider_active = false;
				}
			}

			if ( $meta_divider_width ) {
				if ( 'content' == $meta_divider_width[0] ) {
					$divider_width = '';
				} else {
					$divider_width = $meta_divider_width[0];
				}
			}
		}

		$args['delimiter']   = '<span class="breadcrumb-delimiter"><i class="' . esc_attr( is_rtl() ? 'icon-angle-left' : 'icon-angle-right' ) . '"></i></span>';
		$args['wrap_before'] = '<nav class="woocommerce-breadcrumb' . esc_attr( $divider_active ? ' divider-active' : '' ) . '"><div class="breadcrumb-wrap' . esc_attr( $divider_width ? ' full-divider' : ' content-divider' ) . '"><div class="' . apply_filters( 'molla_breadcrumb_width', esc_attr( $width ) ) . '">' . apply_filters( 'molla_before_breadcrumb', '' ) . '<div class="breadcrumb inner-wrap">';
		$args['wrap_after']  = apply_filters( 'molla_after_breadcrumb', '' ) . '</div></div></div></nav>';
		return $args;
	}
endif;

if ( ! function_exists( 'molla_product_cat_widget_args' ) ) :
	/**
	 * Product Category Widget List.
	 */
	function molla_product_cat_widget_args( $args ) {
		require_once MOLLA_CLASS . '/molla_wc_product_cat_list_walker.php';
		$args['walker'] = new Molla_WC_Product_Cat_List_Walker;
		return $args;
	}
endif;

if ( ! function_exists( 'molla_woocommerce_products_in_custom_tax' ) ) :
	function molla_woocommerce_products_in_custom_tax( $args, $attributes ) {

		if ( ! empty( $attributes['class'] ) ) {
			$classes = explode( ',', $attributes['class'] );

			if ( ! in_array( 'custom_brands', $classes ) ) {
				return $args;
			}

			$args['tax_query'][] = array(
				'taxonomy' => 'product_brand',
				'terms'    => array_map( 'sanitize_title', $classes ),
				'field'    => 'slug',
				'operator' => 'IN',
			);

		}

		return $args;

	}
endif;

if ( ! function_exists( 'molla_woocommerce_disable_product_out') ) :
	function molla_woocommerce_disable_product_out() {
		return 'yes';
	}
endif;

if ( ! function_exists( 'molla_product_related_count' ) ) {
	function molla_product_related_count( $args ) {
		$args['posts_per_page'] = molla_option('single_related_count');
		return $args;
	}
}