<template>
	<div
		v-if="hasVideoSource"
		class="zb__videoBackground-wrapper"
		:data-zion-video-background="getVideoSettings"
	/>
</template>

<script>
export default {
	name: 'VideoBackground',
	props: {
		videoConfig: {
			type: Object,
			required: false
		}
	},
	computed: {
		getVideoSettings () {
			return JSON.stringify(this.videoConfig)
		},
		hasVideoSource () {
			const videoSource = this.videoConfig && this.videoConfig.videoSource ? this.videoConfig.videoSource : 'local'
			if (videoSource === 'youtube' && this.videoConfig.youtubeURL) {
				return true
			} else if (videoSource === 'vimeo' && this.videoConfig.vimeoURL) {
				return true
			} else if (videoSource === 'local' && this.videoConfig.mp4) {
				return true
			}

			return false
		}
	}
}
</script>
