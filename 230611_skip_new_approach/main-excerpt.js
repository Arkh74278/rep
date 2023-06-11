				// Init all carousel
				$wrap.find( '[data-toggle="owl"]' ).each(
					function () {

						if ( $( this ).hasClass( '.thumbnails-outer' ) && !$( this ).closest( '.thumb-horizontal' ).length && $( window ).width() > 991 ) {
							return;
						}

						var $this = $( this ),
							newOwlSettings = $.extend( {}, owlSettings, $this.data( 'owl-options' ) );
						if ( $( 'body' ).hasClass( 'rtl' ) ) {
							newOwlSettings.rtl = true;
						}

						$this.on(
							'initialized.owl.carousel',
							function () {
								$( this ).attr( 'class', $( this ).attr( 'class' ).replace( / c-.*[0-9]/, '' ) );

								// Entrance Animation
								var $animated_items = $( this ).find( '.owl-item .elementor-invisible' ),
									$invisible = $( this ).find( '.owl-item:not(.active) .elementor-invisible' );

								$animated_items.each(
									function () {
										var $this = $( this ),
											data_animation = {};
										data_animation.animation = typeof $this.data( 'settings' )._animation != 'undefined' ? $this.data( 'settings' )._animation : $this.data( 'settings' ).animation;
										data_animation.animation_delay = typeof $this.data( 'settings' )._animation_delay != 'undefined' ? $this.data( 'settings' )._animation_delay : $this.data( 'settings' ).animation_delay;
										if ( $this.hasClass( 'animated-slow' ) ) {
											data_animation.animation_duration = 2000;
										} else if ( $this.hasClass( 'animated-fast' ) ) {
											data_animation.animation_duration = 750;
										} else {
											data_animation.animation_duration = 1250;
										}
										$this.addClass( 'animating-item' );
										$this.attr( 'data-animate-args', JSON.stringify( data_animation ) );
										if ( $this.closest( '.owl-item' ).hasClass( 'active' ) ) {
											$this.addClass( 'owl-animated' );
											$this.css( 'animation-duration', data_animation.animation_duration + 'ms' );
										} else {
											$this.css( 'visibility', 'hidden' );
											$this.css( { 'animation-duration': data_animation.animation_duration + 'ms', 'animation-delay': data_animation.animation_delay + 'ms' } );
											$this.css( { 'transition-duration': data_animation.animation_duration + 'ms', 'transition-delay': data_animation.animation_delay + 'ms' } );
										}
									}
								)
							}
						);
						$this.on(
							'translated.owl.carousel',
							function () {
								var $invisible = $( this ).find( '.owl-item:not(.active) .animating-item' ),
									$visible = $( this ).find( '.owl-item.active .animating-item' );

								$invisible.each(
									function () {
										var invisible_animate_args = $( this ).data( 'animate-args' );
										$( this ).removeClass( 'owl-animated' ).removeClass( invisible_animate_args.animation ).addClass( 'elementor-invisible' );
										$( this ).css( { 'transition-duration': '', 'transition-delay': '' } );
									}
								)
								$visible.each(
									function () {
										self.entranceAnimate( $( this ) );
									}
								)
							}
						);

						if ( !$( this ).hasClass( 'elementor-container' ) ) {
							$this.css( { marginLeft: 0, marginRight: 0, width: '100%' } );
						}

						$this.owlCarousel( newOwlSettings );
					}





		productGallery: ( function () {
			function ProductGallery( $el ) {
				return this.init( $el );
			}

			ProductGallery.prototype.init = function ( $wc_gallery ) {
				var self = this;

				typeof $wc_gallery.data( 'product_gallery' ) == 'undefined' && $wc_gallery.wc_product_gallery();
				self.$wc_gallery = $wc_gallery;
				self.wc_gallery = $wc_gallery.data( 'product_gallery' );
				var $gallery_carousel = $wc_gallery.find( '.product-gallery-carousel' ),
					$thumbcarousel = $wc_gallery.find( '.thumbnails-outer' ),
					$thumbwrap = $wc_gallery.find( '.thumbnails-wrap' );

				if ( $wc_gallery.length ) {
					self.zoom = self.zoom.bind( this );
					self.zoom();
					window.addEventListener( 'resize', self.zoom, { passive: true } );

					if ( $thumbcarousel.hasClass( 'owl-carousel' ) ) {
						if ( !$thumbcarousel.find( '.product-thumb a.active' ).length ) {
							$thumbcarousel.find( '.owl-item:first-of-type .product-thumb a' ).addClass( 'active' );
						}
					} else {
						if ( !$thumbcarousel.find( '.product-thumb a.active' ).length ) {
							$thumbcarousel.children( '.product-thumb:first-of-type' ).find( 'a' ).addClass( 'active' );
						}
					}

					if ( $thumbcarousel.length ) {
						var $top = $thumbcarousel.css( 'top' );
						$top = Number( $top.slice( 0, -2 ) );
						$thumbwrap.find( '.nav-prev, .nav-next' ).removeClass( 'disabled' );

						if ( !$top ) {
							$thumbwrap.find( '.nav-prev' ).addClass( 'disabled' );
						}
						if ( $thumbcarousel.height() <= $thumbwrap.height() ) {
							$thumbwrap.find( '.nav-next' ).addClass( 'disabled' );
						}

						$( 'body' ).on(
							'click',
							'.thumbnails-wrap .nav-next',
							function () {
								var $top = $thumbcarousel.css( 'top' );
								$top = Number( $top.slice( 0, -2 ) );

								if ( $thumbcarousel.height() + $top - 50 > $thumbwrap.height() ) {
									$top -= 50;
								} else {
									$top = $thumbwrap.height() - $thumbcarousel.height();
									$thumbwrap.find( '.nav-next' ).addClass( 'disabled' );
								}
								$top = $top.toFixed( 2 );
								$thumbcarousel.css( 'top', $top + 'px' );

								if ( $top ) {
									$thumbwrap.find( '.nav-prev' ).removeClass( 'disabled' );
								}

								$( window ).trigger( 'scroll' );
							}
						)
						$( 'body' ).on(
							'click',
							'.thumbnails-wrap .nav-prev',
							function () {
								var $top = $thumbcarousel.css( 'top' );
								$top = Number( $top.slice( 0, -2 ) );

								if ( $top < -50 ) {
									$top += 50;
								} else {
									$top = 0;
									$thumbwrap.find( '.nav-prev' ).addClass( 'disabled' );
								}
								$top = $top.toFixed( 2 );
								$thumbcarousel.css( 'top', $top + 'px' );

								if ( $thumbcarousel.height() + Number( $top ) >= $thumbwrap.height() ) {
									$thumbwrap.find( '.nav-next' ).removeClass( 'disabled' );
								}

								$( window ).trigger( 'scroll' );
							}
						)
					}
				}


				$wc_gallery
					.off( 'click', '.woocommerce-product-gallery__image a' )
					.on( 'click', function ( e ) {
						e.preventDefault();
					} );


/* Ark - testing carousel skip. Temp comment 2
				$thumbcarousel.on(
					'click',
					'.product-thumb a',
					function ( e ) {
						if ( $( this ).closest( '.thumbnails-outer' ).hasClass( 'owl-carousel' ) ) {
							$gallery_carousel.data( 'owl.carousel' ).to( $( this ).closest( '.owl-item' ).index(), 300 );
						} else {
							$gallery_carousel.data( 'owl.carousel' ).to( $( this ).parent().index(), 300 );
						}

						$thumbcarousel.find( '.product-thumb a' ).removeClass( 'active' );
						$( this ).addClass( 'active' );
						e.preventDefault();
					}
				)
end of temp skip 2 - Ark */

/* Ark - testing carousel skip. Temp comment
				$gallery_carousel.on(
					'translated.owl.carousel',
					function ( e ) {
						var index = e.item.index;
						if ( $thumbcarousel.hasClass( 'owl-carousel' ) ) {
							$thumbcarousel.data( 'owl.carousel' ).to( index, 100 );
							var $thumb = $thumbcarousel.find( '.owl-item' ).eq( index );
							$thumbcarousel.find( '.product-thumb a' ).removeClass( 'active' );
							$thumb.find( 'a' ).addClass( 'active' );
						} else {
							$thumbcarousel.find( 'a' ).removeClass( 'active' );
							$thumbcarousel.children().eq( index ).find( 'a' ).addClass( 'active' );
						}
					}
				)
end of temp skip Ark */


// Temp new code - Ark
var previousIndex = 0;

$thumbcarousel.on(
    'click',
    '.product-thumb a',
    function (e) {
        if ($(this).find('img').attr('alt') !== 'dummy image') {
            var index = $(this).closest('.thumbnails-outer').hasClass('owl-carousel') ? $(this).closest('.owl-item').index() : $(this).parent().index();
            if (index === previousIndex + 1) {
                $gallery_carousel.data('owl.carousel').to(index, 300);
            } else if (index === previousIndex - 1) {
                $gallery_carousel.data('owl.carousel').to(index - 1, 300);
            }
            previousIndex = index;

            $thumbcarousel.find('.product-thumb a').removeClass('active');
            $(this).addClass('active');
        }
        e.preventDefault();
    }
)

$gallery_carousel.on(
    'translated.owl.carousel',
    function (e) {
        var index = e.item.index;
        if ($(e.target).find('.owl-item').eq(index).find('img').attr('alt') === 'dummy image') {
            if (index === previousIndex + 1) {
                $gallery_carousel.trigger('next.owl.carousel');
            } else if (index === previousIndex - 1) {
                $gallery_carousel.trigger('prev.owl.carousel');
            }
        } else {
            if ($thumbcarousel.hasClass('owl-carousel')) {
                $thumbcarousel.data('owl.carousel').to(index, 100);
                var $thumb = $thumbcarousel.find('.owl-item').eq(index);
                $thumbcarousel.find('.product-thumb a').removeClass('active');
                $thumb.find('a').addClass('active');
            } else {
                $thumbcarousel.find('a').removeClass('active');
                $thumbcarousel.children().eq(index).find('a').addClass('active');
            }
            previousIndex = index;
        }
    }
)


// End of temp new code - Ark



				$wc_gallery.find( '.btn-product-gallery' ).on( 'click', function ( e ) {
					e.preventDefault();
					self.lightbox( e );
				} )

				$( document ).on(
					'found_variation reset_image',
					'.variations_form',
					function ( e ) {
						//		self.zoom($wc_gallery.find('.product-gallery-carousel .woocommerce-product-gallery__image > a'));
						self.zoom();
						// if ( $gallery_carousel.length ) {
						// 	$gallery_carousel.data( 'owl.carousel' ) && $gallery_carousel.data( 'owl.carousel' ).to( 0, 300, true );
						// }
					}
				)

			}
			
			
			
			
			
			

