class RaziiSwiperCarouselOption {
	getOptions(selector, settings, breakpoints, check = true, checkBreak = true){
		const	options = {
					loop            : 'yes' === settings.infinite,
					autoplay        : 'yes' === settings.autoplay,
					speed           : settings.autoplay_speed,
					watchOverflow   : true,
					pagination: {
					   el: selector.find('.swiper-pagination'),
					   type: 'bullets',
					   clickable: true
					},
					on              : {
						init: function() {
							selector.css( 'opacity', 1 );
						}
					},
					breakpoints     : {}
				};

		if (check){
			options.navigation = {
				nextEl: selector.find('.rz-swiper-button-next'),
				prevEl: selector.find('.rz-swiper-button-prev'),
			}
		}

		if (checkBreak){
			options.breakpoints[breakpoints.xs] = { slidesPerView: settings.slidesToShow_mobile, slidesPerGroup: settings.slidesToScroll_mobile  };
			options.breakpoints[breakpoints.md] = { slidesPerView: settings.slidesToShow_tablet, slidesPerGroup: settings.slidesToScroll_tablet  };
			options.breakpoints[breakpoints.lg] = { slidesPerView: settings.slidesToShow, slidesPerGroup: settings.slidesToScroll };
		}

		return options;
	}
}

class RazziTestimonialsCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-testimonials-carousel'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {
		let el = new RaziiSwiperCarouselOption();

		const ops = el.getOptions(this.elements.$container, this.getElementSettings(), elementorFrontend.config.breakpoints);

		return ops;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container.find('.razzi-testimonials-carousel__wrapper'), this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}

		this.getSwiperInit();
	}
}

class RazziCountDownWidgetHandler extends elementorModules.frontend.handlers.Base {

	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-countdown'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getCountDownInit() {
		this.elements.$container.rz_countdown();
	}

	onInit() {
		super.onInit();
		this.getCountDownInit();
	}
}

class RazziFaqsWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-faq'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getFaqsInit() {
		const els = this.elements.$container.find('.box-content');

		els.not(':first-child').find('.faq-desc').slideUp();
		els.on('click', function () {
			var   item = jQuery(this),
				  siblings = item.siblings(item);

			if (els.hasClass('.active')) {
				return;
			}

			siblings.removeClass('active');
			item.addClass('active');
			siblings.find('.faq-desc').slideUp();
			item.find('.faq-desc').slideDown();
		});
	}

	onInit() {
		super.onInit();
		this.getFaqsInit();
	}
}

class RazziMapWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-map'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	get_coordinates(){
		var	data = [],
			index = 0,
			self = this,
			el = self.elements.$container,
			elsMap = el.data('map'),
			local = elsMap.local,
			mapboxClient = mapboxSdk( { accessToken: elsMap.token } ),
			wrapper = el.find('.razzi-map__content').attr('id');

		mapboxgl.accessToken = elsMap.token;

		jQuery.each( local, function( i, val ) {
			mapboxClient.geocoding.forwardGeocode( {
				query       : val,
				autocomplete: false,
				limit       : 1
			} )
				.send()
				.then( function ( response ) {
					if ( response && response.body && response.body.features && response.body.features.length ) {
						var feature = response.body.features[0],
							tab = el.find('.razzi-map__table .box-item');

						tab.eq(i).attr("data-latitude",feature.center[0]);
						tab.eq(i).attr("data-longitude",feature.center[1]);

						var item = {
								"type"    : "Feature",
								'properties': {
									'description': tab.eq(i).html(),
									'icon': 'theatre'
								},
								"geometry": {
									"type"       : "Point",
									"coordinates": [feature.center[0],feature.center[1]]
								}
						};

						if( index == 0 ) {
							var center = [feature.center[0],feature.center[1]];
							self.get_map(wrapper,elsMap,tab, elsMap.token, data, center);
						}

						data.push(item);
						index++;
					}
				} );
		} );

		return data;
	}

	get_map(wrapper,elsMap, tab, accessToken, data, center){
			var map = new mapboxgl.Map( {
				container: wrapper,
				style    : 'mapbox://styles/mapbox/'+ elsMap.mode ,
				center   : center,
				zoom     : elsMap.zom
			} );

			var geocoder = new MapboxGeocoder( {
				accessToken: mapboxgl.accessToken
			} );

			map.addControl( geocoder );

			map.on( 'load', function () {
				map.loadImage( elsMap.marker, function ( error, image ) {
					if ( error ) throw error;
					map.addImage( 'marker', image );
					map.addLayer( {
						"id"    : "points",
						"type"  : "symbol",
						"source": {
							"type": "geojson",
							"data": {
								"type"    : "FeatureCollection",
								"features": data
							}
						},
						"layout": {
							"icon-image": "marker",
							"icon-size" : 1
						}
					} );
				} );

				map.addSource( 'single-point', {
					"type": "geojson",
					"data": {
						"type"    : "FeatureCollection",
						"features": []
					}
				} );

				map.addLayer( {
					"id"    : "point",
					"source": "single-point",
					"type"  : "circle",
					"paint" : {
						"circle-radius": 10,
						"circle-color" : "#007cbf"
					}
				} );

				map.setPaintProperty( 'water', 'fill-color', elsMap.color_1 );
				map.setPaintProperty( 'building', 'fill-color', elsMap.color_2 );

				geocoder.on( 'result', function ( ev ) {
					map.getSource( 'single-point' ).setData( ev.result.geometry );
				} );

				map.on('click', 'points', function (e) {
					var coordinates = e.features[0].geometry.coordinates.slice();
					var description = e.features[0].properties.description;

					while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
						coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
					}

					new mapboxgl.Popup()
						.setLngLat(coordinates)
						.setHTML(description)
						.addTo(map);
				});

				map.on('mouseenter', 'points', function () {
					map.getCanvas().style.cursor = 'pointer';
				});

				map.on('mouseleave', 'points', function () {
					 map.getCanvas().style.cursor = '';
				});
			} );

			tab.on('click', function () {
				var  lat = jQuery(this).data('latitude'),
					 long = jQuery(this).data('longitude');

				map.flyTo({
					center: [lat,long ],
					zoom: elsMap.zom,
					essential: true, // this animation is considered essential with respect to prefers-reduced-motion,
					speed: 3,
					curve: 1,
				});
			});
	}

	onInit() {
		super.onInit();
		this.get_coordinates();
	}
}

class RazziBannerCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-banner-carousel'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {
		let el = new RaziiSwiperCarouselOption();

		const ops = el.getOptions(this.elements.$container, this.getElementSettings(), elementorFrontend.config.breakpoints);

		return ops;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container, this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}

		this.getSwiperInit();
	}
}

class RazziTestimonialsCarousel2WidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-testimonials-carousel-2'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {

		let el = new RaziiSwiperCarouselOption();

		const ops = el.getOptions(this.elements.$container, this.getElementSettings(), elementorFrontend.config.breakpoints, false);

		ops.spaceBetween  = 30;

		return ops;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container, this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}
		this.getSwiperInit();
	}
}

class RazziBrandsCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-brands-carousel'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {

		let el = new RaziiSwiperCarouselOption();

		const ops = el.getOptions(this.elements.$container, this.getElementSettings(), elementorFrontend.config.breakpoints);

		return ops;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container.find('.list-brands__wrapper'), this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}
		this.getSwiperInit();
	}
}

class RazziSlidesWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-slides-elementor'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {

		const 	self 		= this;
		const 	container 	= self.elements.$container;
		const 	settings 	= self.getElementSettings();
		const 	breakpoints = elementorFrontend.config.breakpoints;

		const 	options = {
					loop            : 'yes' === settings.infinite,
					autoplay        : 'yes' === settings.autoplay,
                	delay           : settings.delay,
					speed           : settings.autoplay_speed,
					watchOverflow   : true,
                	lazy            : 'yes' === settings.lazyload,
                	effect          : settings.effect,
				 	navigation: {
				 		nextEl: container.find('.rz-swiper-button-next'),
						prevEl: container.find('.rz-swiper-button-prev'),
	                },
					pagination: {
					   el: container.find('.swiper-pagination'),
					   type: 'bullets',
					   clickable: true
					},
			 		fadeEffect: {
	                    crossFade: true
	                },
					on              : {
						init: function() {
							container.css( 'opacity', 1 );
						}
					}
				};

		return options;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container.find('.razzi-slides-elementor__wrapper'), this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}
		this.getSwiperInit();
	}
}

class RazziIsolateSlidesWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-isolate-slides'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {

		const 	self 		= this;
		const 	container 	= self.elements.$container;
		const 	settings 	= self.getElementSettings();
		const 	breakpoints = elementorFrontend.config.breakpoints;

		const 	options = {
					loop            : 'yes' === settings.infinite,
					autoplay        : 'yes' === settings.autoplay,
                	delay           : settings.delay,
					speed           : settings.autoplay_speed,
					watchOverflow   : true,
                	lazy            : 'yes' === settings.lazyload,
                	effect          : settings.effect,
				 	navigation: {
				 		nextEl: container.find('.rz-swiper-button-next'),
						prevEl: container.find('.rz-swiper-button-prev'),
	                },
					pagination: {
					   el: container.find('.swiper-pagination'),
					   type: 'bullets',
					   clickable: true
					},
			 		fadeEffect: {
	                    crossFade: true
	                },
					on              : {
						init: function() {
							container.css( 'opacity', 1 );
						}
					}
				};

		return options;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container.find('.razzi-isolate-slides__content-wrapper'), this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}
		this.getSwiperInit();
	}
}

class RazziPostCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-posts-carousel'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {

		let el = new RaziiSwiperCarouselOption();

		const ops = el.getOptions(this.elements.$container, this.getElementSettings(), elementorFrontend.config.breakpoints);

		ops.spaceBetween = 30;

		return ops;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container.find('.list-posts'), this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}

		this.getSwiperInit();
	}
}

class RazziPostCarousel2WidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-posts-carousel-2'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {
		let el = new RaziiSwiperCarouselOption();

		const ops = el.getOptions(this.elements.$container, this.getElementSettings(), elementorFrontend.config.breakpoints);

		ops.spaceBetween = 30;

		return ops;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container.find('.list-posts'), this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}
		this.getSwiperInit();
	}
}

class RazziBannerVideoWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-banner-video'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getPopupOption() {
		const 	options = {
					type: 'iframe',
	            	mainClass: 'mfp-fade',
		            removalDelay: 300,
		            preloader: false,
		            fixedContentPos: false,
		            iframe: {
						markup: '<div class="mfp-iframe-scaler">' +
								'<div class="mfp-close"></div>' +
								'<iframe class="mfp-iframe" frameborder="0" allow="autoplay"></iframe>' +
								'</div>',
		                patterns: {
		                    youtube: {
		                        index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).

		                        id: 'v=', // String that splits URL in a two parts, second part should be %id%
		                        src: 'https://www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
		                    },
		                    vimeo: {
		                        index: 'vimeo.com/',
		                        id: '/',
		                        src: '//player.vimeo.com/video/%id%?autoplay=1'
		                    }
		                },

		                srcAction: 'iframe_src', // Templating object key. First part defines CSS selector, second attribute. "iframe_src" means: find "iframe" and set attribute "src".
		            }
				};

		return options;
	}

	getPopupInit() {
		this.elements.$container.find('.razzi-banner-video__play').magnificPopup( this.getPopupOption() )
	}

	onInit() {
		super.onInit();
		this.getPopupInit();
	}
}

class RazziLookbookSliderWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-lookbook-slider-elementor'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {

		const 	container 	= this.elements.$container;
		const 	settings 	= this.getElementSettings();
		const 	breakpoints = elementorFrontend.config.breakpoints;

		const 	options = {
					loop            : 'yes' === settings.infinite,
					autoplay        : 'yes' === settings.autoplay,
                	delay           : settings.delay,
					speed           : settings.autoplay_speed,
					watchOverflow   : true,
                	lazy            : 'yes' === settings.lazyload,
                	effect          : settings.effect,
				 	navigation: {
	                    nextEl: container.find('.rz-swiper-button-next'),
						prevEl: container.find('.rz-swiper-button-prev'),
	                },
					pagination: {
					   el: container.find('.swiper-pagination'),
					   type: 'bullets',
					   clickable: true
					},
			 		fadeEffect: {
	                    crossFade: true
	                },
					on              : {
						init: function() {
							container.css( 'opacity', 1 );

							if(container.find('.item-slider').length > 1){
								container.find('.slick-slide-block__img .rz-swiper-button').css({'display':'block', 'cursor':'pointer'});
							}
						}
					}
				};

		return options;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container, this.getSwiperOption() );
	}

	lookBookHandle(){
		const 	container = this.elements.$container;
		const 	item = container.find('.razzi-lookbook-item');

        container.on('click', '.razzi-lookbook-item', function (e) {
            var el = jQuery(this),
                siblings = el.siblings();

            el.toggleClass('active');
            siblings.removeClass('active');
        });

        jQuery(document.body).on('click', function (evt) {

            if (jQuery(evt.target).closest(item).length > 0) {
                return;
            }

            item.removeClass('active');
        });
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}

		this.getSwiperInit();
		this.lookBookHandle();
	}
}

class RazziBeforeAfterImagesWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-before-after-images'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {
		const 	settings = this.getElementSettings();

		let el = new RaziiSwiperCarouselOption();

		const ops = el.getOptions(this.elements.$container, settings, elementorFrontend.config.breakpoints, true, false);

		ops.touchRatio = 0;
		ops.delay = settings.delay;
		ops.effect = 'fade';

		return ops;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container, this.getSwiperOption() );
	}

	changeImagesHandle() {
		const container = this.elements.$container;

        container.imagesLoaded( function () {
            container.find( '.box-thumbnail' ).imageslide();
        } );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}
		this.getSwiperInit();
		this.changeImagesHandle();
	}
}

class RazziTestimonialsGridFlexibleWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-testimonials-grid'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	ItemsFlexibleHandle() {
		const 	container = this.elements.$container;
		const 	settings = this.getElementSettings();

        container.imagesLoaded(function () {
            container.find('.testimonials-wrapper').masonryGrid({
                'columns': settings.columns,
            });
        });
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}
		this.ItemsFlexibleHandle();
	}
}

class RazziIconBoxCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-icons-box-carousel'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperOption() {

		const 	self 		= this;
		const 	container 	= self.elements.$container;
		const 	settings 	= self.getElementSettings();

		const 	options = {
					loop            : 'yes' === settings.infinite,
					autoplay        : 'yes' === settings.autoplay,
					speed           : settings.autoplay_speed,
					watchOverflow   : true,
					navigation: {
						nextEl: container.find('.rz-swiper-button-next'),
						prevEl: container.find('.rz-swiper-button-prev'),
	                },
					pagination: {
					   el: container.find('.swiper-pagination'),
					   type: 'bullets',
					   clickable: true
					},
					on              : {
						init: function() {
							container.find('.razzi-icons-box-carousel__wrapper').css( 'opacity', 1 );
						}
					},
					breakpoints     : {
	                    0: {
	                        slidesPerView : settings.slidesToShow_mobile  ? settings.slidesToShow_mobile : 2,
	                        slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : 2
	                    },

	                    380: {
	                        slidesPerView : settings.slidesToShow_mobile  ? settings.slidesToShow_mobile : 3,
	                        slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : 3
	                    },

	                    546 : {
	                        slidesPerView : settings.slidesToShow_tablet  ? settings.slidesToShow_tablet : 4,
	                        slidesPerGroup: settings.slidesToScroll_tablet ? settings.slidesToScroll_tablet : 4
	                    },
	                    768 : {
	                        slidesPerView : settings.slidesToShow_tablet  ? settings.slidesToShow_tablet : 5,
	                        slidesPerGroup: settings.slidesToScroll_tablet ? settings.slidesToScroll_tablet : 5
	                    },
	                    1025: {
	                        slidesPerView : settings.slidesToShow,
	                        slidesPerGroup: settings.slidesToScroll
	                    },
	                }
				};

		return options;
	}

	getSwiperInit() {
		new Swiper( this.elements.$container.find('.razzi-icons-box-carousel__wrapper'), this.getSwiperOption() );
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}
		this.getSwiperInit();
	}
}

class RazziImagesCarouselWidgetHandler extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.razzi-images-carousel'
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );

		return {
			$container: this.$element.find( selectors.container )
		};
	}

	getSwiperInit() {
		const settings = this.getElementSettings();

		var options = {
            watchOverflow: true,
            loop: settings.infinite == 'yes' ? true : false,
            autoplay: settings.autoplay == 'yes' ? true : false,
            speed: settings.autoplay_speed,
            navigation: {
                nextEl: this.elements.$container.find('.rz-swiper-button-next'),
                prevEl: this.elements.$container.find('.rz-swiper-button-prev'),
            },
            pagination: {
                el: this.elements.$container.find('.swiper-pagination'),
                clickable: true
            },
            on: {
                init: function () {
                    this.$el.css('opacity', 1);
                }
            },
            breakpoints: {
                300: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                },
                480: {
                    slidesPerView: settings.slidesToShow_mobile ? settings.slidesToShow_mobile : razziData.mobile_portrait,
                    slidesPerGroup: settings.slidesToScroll_mobile ? settings.slidesToScroll_mobile : razziData.mobile_portrait,
                },
                768: {
                    slidesPerView: settings.slidesToShow_tablet ? settings.slidesToShow_tablet : 3,
                    slidesPerGroup: settings.slidesToScroll_tablet ? settings.slidesToScroll_tablet : 3,
                },
                1024: {
                    slidesPerView: settings.slidesToShow > 4 ? 4 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 4 ? 4 : settings.slidesToScroll,
                },
                1366: {
                    slidesPerView: settings.slidesToShow > 5 ? 5 : settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll > 5 ? 5 : settings.slidesToScroll
                },
                1500: {
                    slidesPerView: settings.slidesToShow,
                    slidesPerGroup: settings.slidesToScroll
                }
            }
        };

        new Swiper(this.elements.$container.find('.swiper-container'), options);
	}

	onInit() {
		super.onInit();

		if ( ! this.elements.$container.length ) {
			return;
		}

		this.getSwiperInit();
	}
}


jQuery( window ).on( 'elementor/frontend/init', () => {
	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-testimonials-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziTestimonialsCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-countdown.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziCountDownWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-faq.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziFaqsWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-map.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziMapWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-banner-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziBannerCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-testimonials-carousel-2.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziTestimonialsCarousel2WidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-brands-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziBrandsCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-slides.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziSlidesWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-isolate-slides.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziIsolateSlidesWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-posts-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziPostCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-posts-carousel-2.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziPostCarousel2WidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-banner-video.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziBannerVideoWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-lookbook-slider.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziLookbookSliderWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-before-after-images.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziBeforeAfterImagesWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-testimonials-grid.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziTestimonialsGridFlexibleWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-icons-box-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziIconBoxCarouselWidgetHandler, { $element } );
	} );

	elementorFrontend.hooks.addAction( 'frontend/element_ready/razzi-images-carousel.default', ( $element ) => {
		elementorFrontend.elementsHandler.addHandler( RazziImagesCarouselWidgetHandler, { $element } );
	} );

} );