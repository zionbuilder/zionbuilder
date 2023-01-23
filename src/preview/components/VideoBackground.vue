<template>
	<div v-if="hasVideoSource" class="zb__videoBackground-wrapper" :data-zion-video-background="getVideoSettings" />
</template>

<script lang="ts" setup>
import { isEqual } from 'lodash-es';
import { computed, nextTick, onMounted, watch } from 'vue';

const props = withDefaults(defineProps<{ videoConfig: any }>(), {
	videoConfig: () => ({}),
});

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
		if (!hasVideoSource.value) {
			return;
		}

		if (!isEqual(newValue, oldValue)) {
			if (videoInstance.value) {
				videoInstance.value.destroy();
			}

			nextTick(() => {
				const script = window.document.getElementById('znpb-editor-iframe')?.contentWindow?.ZBVideoBg;
				if (script) {
					videoInstance.value = new script(elementRef.value, props.videoConfig);
				} else {
					console.error('video script not found');
				}
			});
		}
	},
);

onMounted(() => {
	if (!hasVideoSource.value) {
		return;
	}

	if (Object.keys(props.videoConfig).length > 0) {
		const script = window.document.getElementById('znpb-editor-iframe')?.contentWindow?.ZBVideoBg;
		if (script) {
			videoInstance.value = new script(elementRef.value, props.videoConfig);
		} else {
			console.error('video script not found');
		}
	}
});
</script>
