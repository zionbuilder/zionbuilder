(function ($) {
	window.ZionBuilderFrontend.registerScript('imageSlider', {
		initSlider(el, config) {
			return new window.Swiper(el, config)
		},

		getConfig(element, extraData = {}) {
			const $slider = $(element)
			const configAttr = $slider.attr('data-zion-slider-config')
			const elementConfig = configAttr ? JSON.parse(configAttr) : {}
			const sliderConfig = {
				autoplay: true,
				autoHeight: true
			}

			// Pagination
			if (elementConfig.pagination) {
				sliderConfig.pagination = {
					el: '.swiper-pagination'
				}
			}

			// Navigation
			if (elementConfig.arrows) {
				sliderConfig.navigation = {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev'
				}
			}

			// Slides to show and breakpoints
			let slidesPerView = 1
			let slidesToShow = elementConfig.slides_to_show || 1
			const breakpoints = {}

			if (typeof slidesToShow === 'number') {
				slidesPerView = slidesToShow
			} else if (typeof slidesToShow === 'object') {
				slidesPerView = typeof slidesToShow.default !== 'undefined' ? slidesToShow.default : 1
				const mediaQueries = {
					default: 992,
					laptop: 768,
					tablet: 576,
					mobile: 0,
				}

				let lastValue = false
				Object.keys(mediaQueries).forEach(key => {
					const value = mediaQueries[key]
					if (typeof slidesToShow[key] !== 'undefined') {
						breakpoints[value] = {
							slidesPerView: slidesToShow[key]
						}
						lastValue = slidesToShow[key]
					} else if (lastValue !== false) {
						breakpoints[value] = {
							slidesPerView: lastValue
						}
					}
				})
			}

			sliderConfig.slidesPerView = slidesPerView
			sliderConfig.breakpoints = breakpoints


			return {
				...sliderConfig,
				...elementConfig.rawConfig
			}
		},

		run(scope) {
			const $sliders = $(scope).find('.zb-el-imageSlider').addBack('.zb-el-imageSlider')
			const script = this
			$.each($sliders, function (i, slider) {
				script.initSlider(slider, script.getConfig(slider))
			})
		}
	})
})(window.jQuery)
