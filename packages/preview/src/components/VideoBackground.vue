<template>
	<div
		v-if="hasVideoSource"
		class="zb__videoBackground-wrapper"
		:data-zion-video-background="getVideoSettings"
	/>
</template>

<script>
import { isEqual } from 'lodash-es'

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
	},
	watch: {
		videoConfig (newValue, oldValue) {
			if (!this.hasVideoSource) {
				return
			}

			if (!isEqual(newValue, oldValue)) {
				if (this.videoInstance) {
					this.videoInstance.destroy()
				}

				this.$nextTick(() => {
					this.videoInstance = new window.ZBVideoBg(this.$el, this.videoConfig)
				})
			}
		}
	},
	mounted () {
		if (Object.keys(this.videoConfig).length > 0) {
			this.videoInstance = new window.ZBVideoBg(this.$el, this.videoConfig)
		}
	}
}
</script>
