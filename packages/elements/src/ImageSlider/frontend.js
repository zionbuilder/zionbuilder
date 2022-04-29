window.zbFrontend = window.zbFrontend || [];
window.zbFrontend.scripts = window.zbFrontend.scripts || {};

function useSwiper() {
	function getConfig(sliderEl) {
		const configAttr = sliderEl.dataset.zionSliderConfig;
		const elementConfig = configAttr ? JSON.parse(configAttr) : {};
		const sliderConfig = {
			autoplay: true,
			autoHeight: true,
		};

		// Pagination
		if (elementConfig.pagination) {
			sliderConfig.pagination = {
				el: '.swiper-pagination',
			};
		}

		// Navigation
		if (elementConfig.arrows) {
			sliderConfig.navigation = {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			};
		}

		// Slides to show and breakpoints
		let slidesPerView = 1;
		let slidesToShow = elementConfig.slides_to_show || 1;
		const breakpoints = {};

		if (typeof slidesToShow === 'number') {
			slidesPerView = slidesToShow;
		} else if (typeof slidesToShow === 'object') {
			slidesPerView = typeof slidesToShow.default !== 'undefined' ? slidesToShow.default : 1;

			let lastValue = false;
			Object.keys(window.zbFrontendResponsiveDevicesMobileFirst).forEach(key => {
				const value = window.zbFrontendResponsiveDevicesMobileFirst[key];
				if (typeof slidesToShow[key] !== 'undefined') {
					breakpoints[value] = {
						slidesPerView: slidesToShow[key],
					};
					lastValue = slidesToShow[key];
				} else if (lastValue !== false) {
					breakpoints[value] = {
						slidesPerView: lastValue,
					};
				}
			});
		}

		sliderConfig.slidesPerView = slidesPerView;
		sliderConfig.breakpoints = breakpoints;

		return {
			...sliderConfig,
			...elementConfig.rawConfig,
			observer: true,
			observeParents: true,
		};
	}

	function initSlider(sliderEl, config) {
		return new window.Swiper(sliderEl, config);
	}

	function runAll(scope = document) {
		const sliders = scope.querySelectorAll('.zb-el-imageSlider');
		sliders.forEach(sliderEl => {
			const config = getConfig(sliderEl);
			sliderEl.zbSwiper = initSlider(sliderEl, config);
		});
	}

	return {
		getConfig,
		initSlider,
		runAll,
	};
}

// Register script
window.zbFrontend.scripts.swiper = useSwiper();
// Run on current page
window.zbFrontend.scripts.swiper.runAll();
