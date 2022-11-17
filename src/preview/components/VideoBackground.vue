<template>
	<div v-if="hasVideoSource" class="zb__videoBackground-wrapper" :data-zion-video-background="getVideoSettings" />
</template>

<script>
import { isEqual } from 'lodash-es';

export default {
	name: 'VideoBackground',
	props: {
		videoConfig: {
			type: Object,
			required: false,
			default: () => {
				return {};
			},
		},
	},
	computed: {
		getVideoSettings() {
			return JSON.stringify(this.videoConfig);
		},
		hasVideoSource() {
			const videoSource = this.videoConfig && this.videoConfig.videoSource ? this.videoConfig.videoSource : 'local';
			if (videoSource === 'youtube' && this.videoConfig.youtubeURL) {
				return true;
			} else if (videoSource === 'vimeo' && this.videoConfig.vimeoURL) {
				return true;
			} else if (videoSource === 'local' && this.videoConfig.mp4) {
				return true;
			}

			return false;
		},
	},
	watch: {
		videoConfig(newValue, oldValue) {
			if (!this.hasVideoSource) {
				return;
			}

			if (!isEqual(newValue, oldValue)) {
				if (this.videoInstance) {
					this.videoInstance.destroy();
				}

				this.$nextTick(() => {
					const script = window.document.getElementById('znpb-editor-iframe')?.contentWindow?.ZBVideoBg;
					if (script) {
						this.videoInstance = new script(this.$el, this.videoConfig);
					} else {
						console.error('video script not found');
					}
				});
			}
		},
	},
	mounted() {
		if (!this.hasVideoSource) {
			return;
		}

		if (Object.keys(this.videoConfig).length > 0) {
			const script = window.document.getElementById('znpb-editor-iframe')?.contentWindow?.ZBVideoBg;
			if (script) {
				this.videoInstance = new script(this.$el, this.videoConfig);
			} else {
				console.error('video script not found');
			}
		}
	},
};
</script>
