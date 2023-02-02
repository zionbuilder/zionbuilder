<template>
	<div
		v-if="hasVideoSource"
		ref="elementRef"
		class="zb__videoBackground-wrapper zbjs_video_background hg-video-bg__wrapper"
		:data-zion-video-background="getVideoSettings"
	/>
</template>

<script lang="ts" setup>
import { isEqual } from 'lodash-es';
import { computed, ref, Ref, nextTick, onMounted, watch } from 'vue';

const props = withDefaults(defineProps<{ videoConfig: any }>(), {
	videoConfig: () => ({}),
});

const elementRef = ref(null);
const videoInstance: Ref<Video | null> = ref(null);

const getVideoSettings = computed(() => JSON.stringify(props.videoConfig));
const hasVideoSource = computed(() => {
	const videoSource = props.videoConfig && props.videoConfig.videoSource ? props.videoConfig.videoSource : 'local';
	if (videoSource === 'youtube' && props.videoConfig.youtubeURL) {
		return true;
	} else if (videoSource === 'vimeo' && props.videoConfig.vimeoURL) {
		return true;
	} else if (videoSource === 'local' && props.videoConfig.mp4) {
		return true;
	}

	return false;
});

watch(
	() => props.videoConfig,
	(newValue, oldValue) => {
		if (!isEqual(newValue, oldValue)) {
			nextTick(() => {
				initVideo();
			});
		}
	},
);

function initVideo() {
	if (!hasVideoSource.value) {
		return;
	}

	if (videoInstance.value) {
		videoInstance.value.destroy();
	}

	const script = window.document.getElementById('znpb-editor-iframe')?.contentWindow?.zbScripts.video;

	if (script) {
		videoInstance.value = new script(elementRef.value, {
			...props.videoConfig,
			isBackgroundVideo: true,
		});
	} else {
		console.error('video script not found');
	}
}

onMounted(() => {
	initVideo();
});
</script>
