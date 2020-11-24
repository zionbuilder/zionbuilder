(function ($) {
	window.ZionBuilderFrontend.registerScript('imageSlider', {
		initSlider (el, config) {
			return new window.Swiper(el, config)
		},

		getConfig (element, extraData = {}) {
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

			return {
				...sliderConfig,
				...elementConfig.rawConfig
			}
		},

		run (scope) {
			const $sliders = $(scope).find('.zb-el-imageSlider').addBack('.zb-el-imageSlider')
			const script = this
			$.each($sliders, function (i, slider) {
				const config = script.getConfig(slider)
				// eslint-disable-next-line no-new
				script.initSlider(slider, config)
			})
		}
	})
})(window.jQuery)
