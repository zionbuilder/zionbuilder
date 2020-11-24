<template>
	<div>
		<slot name="start" />

		<div
			class="swiper-container"
			:data-zion-slider-config="getElementOptions"
			ref="sliderWrapper"
		>

			<div class="swiper-wrapper">
				<img
					v-for="(slide, i) in options.images"
					:key="i"
					:src="slide.image"
					class="zb-el-imageSlider__img swiper-slide"
				>

			</div>

			<!-- Add Pagination -->
			<div
				v-if="options.dots"
				class="swiper-pagination"
				ref="pagination"
			></div>

			<!-- Arrows -->
			<template v-if="options.arrows">
				<div class="swiper-button-prev"></div>
				<div class="swiper-button-next"></div>
			</template>

		</div>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'image_slider',
	props: ['options', 'element', 'api'],
	mounted () {
		this.runScript()
	},
	watch: {
		options () {
			this.$nextTick(() => {
				this.runScript()
			})
		}
	},
	computed: {
		getElementOptions () {
			const config = this.options ? {
				arrows: this.options.arrows,
				pagination: this.options.dots,
				rawConfig: {
					observer: true,
					loop: this.options.infinite,
					slidesPerView: this.options.slides_to_show,
					slidesPerGroup: this.options.slides_to_scroll
				}
			} : {}

			// if autoplay is enabled set the speed
			if (this.options.autoplay) {
				config.rawConfig.autoplay = {
					delay: this.options.autoplay_delay
				}
			}

			return JSON.stringify(config)
		}
	},
	methods: {
		runScript () {
			const script = window.ZionBuilderFrontend.getScript('imageSlider')
			const compInstance = this

			if (script) {
				const config = script.getConfig(this.$refs.sliderWrapper)

				config.on = {
					beforeDestroy: function () {
						// clear pagination as it is not deleted during destroy
						if (compInstance.$refs.pagination) {
							compInstance.$refs.pagination.innerHTML = ''
						}
					}
				}

				if (this.slider) {
					config.initialSlide = this.slider.realIndex
					this.slider.destroy()
				}

				this.slider = script.initSlider(this.$refs.sliderWrapper, config)
			}
		}
	},
	beforeUnmount () {
		if (this.slider) {
			this.slider.destroy()
		}
	}
}
</script>
