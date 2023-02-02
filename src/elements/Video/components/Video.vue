<template>
	<div ref="root">
		<slot name="start" />

		<div class="zb-el-zionVideo-wrapper" />
		<div class="znpb-el-zionVideo--bgOverlay"></div>
		<component
			:is="imageOverlayTag"
			v-if="options.use_image_overlay"
			ref="videoOverlay"
			class="zb-el-zionVideo-overlay zb-el-zionVideo-overlay--inline"
			:style="getImageOverlayStyles"
		>
			<span v-if="options.show_play_icon" class="zb-el-zionVideo-play-button zion-play-filled">
				<svg class="zb-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
					<path d="M55.3 32 8.7 5.1v53.8L55.3 32z" />
				</svg>
			</span>
		</component>

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
import { isEqual } from 'lodash-es';
import { ref, watch, nextTick, onMounted, computed } from 'vue';

const props = defineProps<{
	options: {
		video_config: {
			videoSource: string;
			videoUrl: string;
			videoId: string;
			videoPoster: string;
			mp4: string;
			webm: string;
			ogg: string;
		};
		use_image_overlay: boolean;
		show_play_icon: boolean;
		image: {
			image: string;
		};
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const root = ref(null);
const videoOverlay = ref(null);
let videoPlayer = null;

onMounted(() => {
	runScript();
});

const watchedValues = computed(() => {
	return [props.options.video_config, props.options.use_image_overlay, props.options.show_play_icon];
});

watch(watchedValues, (newValue, oldValue) => {
	if (isEqual(newValue, oldValue)) {
		return;
	}

	if (videoPlayer) {
		if (videoOverlay.value) {
			window.jQuery(videoOverlay.value).show();
		}
	}

	nextTick(() => {
		runScript();
	});
});

function runScript() {
	console.log(window.zbScripts);
	if (window.zbScripts?.video) {
		if (videoPlayer) {
			videoPlayer.destroy();
		}

		if (root.value) {
			videoPlayer = new window.zbScripts.video(root.value, getElementOptions.value);
			videoPlayer.init();
		}
	}
}

const videoSourceModel = computed(() => {
	return props.options.video_config || {};
});

const getElementOptions = computed(() => {
	console.log(props.options);
	return {
		use_image_overlay: props.options.use_image_overlay,
		...videoSourceModel.value,
	};
});

const imageSrc = computed(() => {
	return (props.options.image || {}).image;
});

const imageOverlayTag = computed(() => {
	return 'div';
});

const getImageOverlayStyles = computed(() => {
	const styles: Record<string, string> = {};
	if (props.options.use_image_overlay && imageSrc.value) {
		styles['background-image'] = `url(${imageSrc.value})`;
	}
	return styles;
});
</script>

<style lang="scss">
.znpb-el-zionVideo--bgOverlay {
	display: block;
	position: absolute;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
}
</style>
