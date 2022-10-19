<template>
	<div class="swiper">
		<slot name="start" />

		<div ref="sliderWrapper" class="swiper-container" :data-zion-slider-config="elementOptions">
			<div class="swiper-wrapper">
				<div v-for="(slide, i) in options.images" :key="i" class="swiper-slide">
					<img :src="slide.image" />
				</div>
			</div>

			<!-- Add Pagination -->
			<div v-if="options.dots" ref="pagination" class="swiper-pagination"></div>

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
	name: 'ImageSlider',
};
</script>

<script lang="ts" setup>
import { onMounted, watch, computed, onBeforeUnmount, nextTick, ref } from 'vue';

interface IProps {
	/**
	 * The Name of the icon - String
	 */
	options: Record<string, unknown>;

	element: ZionElement;

	api: Record<string, unknown>;
}

const props = defineProps<IProps>();
const { mobileFirstResponsiveDevices } = window.zb.editor.useResponsiveDevices();

// Vars
let slider = null;

// refs
const sliderWrapper = ref(null);
const pagination = ref(null);

// Computed
const elementOptions = computed(() => {
	const config = props.options
		? {
				arrows: props.options.arrows,
				pagination: props.options.dots,
				slides_to_show: props.options.slides_to_show,
				slides_to_scroll: props.options.slides_to_scroll,
				rawConfig: {
					observer: true,
					autoplay: props.options.autoplay,
					speed: props.options.speed || 300,
				},
		  }
		: {};

	// if autoplay is enabled set the speed
	if (props.options.autoplay) {
		config.rawConfig.autoplay = {
			delay: props.options.autoplay_delay,
		};
	}

	return JSON.stringify(config);
});

// Watchers
watch(elementOptions, () => {
	nextTick(() => runScript());
});

/**
 * Run the script again if the responsive devices were changed
 */
watch(mobileFirstResponsiveDevices, newValue => {
	window.zbFrontendResponsiveDevicesMobileFirst = newValue;
	runScript();
});

// Methods
function runScript() {
	const script = window.zbFrontend.scripts.swiper;

	if (script) {
		const config = script.getConfig(sliderWrapper.value);

		config.on = {
			beforeDestroy: function () {
				// clear pagination as it is not deleted during destroy
				if (pagination.value) {
					pagination.value.innerHTML = '';
				}
			},
		};

		if (slider) {
			config.initialSlide = slider.realIndex;
			slider.destroy();
		}

		slider = script.initSlider(sliderWrapper.value, config);
	}
}

// Lifecycle
onMounted(() => {
	runScript();
});

onBeforeUnmount(() => {
	if (slider) {
		slider.destroy();
	}
});
</script>
