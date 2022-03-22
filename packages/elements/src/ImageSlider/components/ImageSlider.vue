<template>
	<div>
		<slot name="start" />

		<div
			class="swiper-container"
			:data-zion-slider-config="elementOptions"
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

<script lang="ts">
export default {
	name: 'image_slider'
}
</script>

<script lang="ts" setup>
import { onMounted, watch, computed, onBeforeUnmount, nextTick, ref } from 'vue'
import { useResponsiveDevices } from '@zb/components'

interface IProps {
	/**
	 * The Name of the icon - String
	 */
	options: Object,

	element: Object,

	api: Object
}

const props = defineProps<IProps>();
const { mobileFirstResponsiveDevices } = useResponsiveDevices()

// Vars
let slider = null

// refs
const sliderWrapper = ref(null)
const pagination = ref(null)

// Computed
const elementOptions = computed(() => {
	const config = props.options ? {
		arrows: props.options.arrows,
		pagination: props.options.dots,
		slides_to_show: props.options.slides_to_show,
		rawConfig: {
			observer: true,
			loop: props.options.infinite,
			slidesPerGroup: props.options.slides_to_scroll,
			autoplay: props.options.autoplay
		}
	} : {}

	// if autoplay is enabled set the speed
	if (props.options.autoplay) {
		config.rawConfig.autoplay = {
			delay: props.options.autoplay_delay
		}
	}

	return JSON.stringify(config)
})

// Watchers
watch(elementOptions, () => {
	nextTick(() => runScript())
})

/**
 * Run the script again if the responsive devices were changed
 */
watch(mobileFirstResponsiveDevices, (newValue) => {
	window.zbFrontendResponsiveDevicesMobileFirst = newValue
	runScript()
})

// Methods
function runScript () {
	const script = window.zbFrontend.scripts.swiper

	if (script) {
		const config = script.getConfig(sliderWrapper.value)

		config.on = {
			beforeDestroy: function () {
				// clear pagination as it is not deleted during destroy
				if (pagination.value) {
					pagination.value.innerHTML = ''
				}
			}
		}

		if (slider) {
			config.initialSlide = slider.realIndex
			slider.destroy()
		}

		slider = script.initSlider(sliderWrapper.value, config)
	}
}

// Lifecycle
onMounted(() => {
	runScript()
})

onBeforeUnmount(() => {
	if (slider) {
		slider.destroy()
	}
})
</script>
