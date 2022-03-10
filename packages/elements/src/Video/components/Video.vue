<template>
	<div
		:data-zion-video="getElementOptions"
		ref="root"
	>
		<slot name="start" />

		<div
			class="zb-el-zionVideo-wrapper"
			ref="videoPlayer"
		/>

		<component
			:is="imageOverlayTag"
			v-if="options.use_image_overlay"
			v-bind="imageOverlayAttributes"
			class="zb-el-zionVideo-overlay zb-el-zionVideo-overlay--inline"
			:style="getImageOverlayStyles"
			ref="videoOverlay"
		>
			<span
				v-if="options.show_play_icon"
				class="zb-el-zionVideo-play-button zion-play-filled"
			><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M55.3 32 8.7 5.1v53.8L55.3 32z"/></svg></span>
		</component>

		<slot name="end" />
	</div>
</template>

<script>
import { isEqual } from 'lodash-es'
import { ref, watch, nextTick, onMounted, computed } from 'vue'
export default {
	name: 'zion_video',
	props: ['options', 'element', 'api'],
	setup (props) {
		const root = ref(null)
		const videoOverlay = ref(null)
		let videoPlayer = null

		onMounted(() => {
			runScript()
		})

		const watchedValues = computed(() => {
			return [
				props.options.video_config,
				props.options.use_image_overlay,
				props.options.show_play_icon
			]
		})

		watch(
			watchedValues
			, (newValue, oldValue) => {
				if (isEqual(newValue, oldValue)) {
					return
				}

				if (videoPlayer) {
					videoPlayer.destroy()
					if (videoOverlay.value) {
						window.jQuery(videoOverlay.value).show()
					}
				}

				nextTick(() => {
					runScript()
				})
			})

		function runScript () {
			const script = window.ZionBuilderFrontend.getScript('video')

			if (script) {
				videoPlayer = script.initVideo(root.value)
			}
		}

		return {
			root,
			videoOverlay,
			videoPlayer
		}
	},

	computed: {
		videoSourceModel () {
			return this.options.video_config || {}
		},

		getElementOptions () {
			return JSON.stringify({
				video_config: this.videoSourceModel,
				use_image_overlay: this.options.use_image_overlay,
				use_modal: this.options.use_modal
			})
		},

		videoSource () {
			return this.videoSourceModel.videoSource || 'local'
		},
		videoSourceURL () {
			if (this.videoSource === 'local' && this.videoSourceModel.mp4) {
				return this.videoSourceModel.mp4
			}

			if (this.videoSource === 'youtube' && this.videoSourceModel.youtubeURL) {
				return this.videoSourceModel.youtubeURL
			}

			if (this.videoSource === 'vimeo' && this.videoSourceModel.vimeoURL) {
				return this.videoSourceModel.vimeoURL
			}

			return false
		},
		useImageOverlay () {
			return this.options.use_image_overlay
		},
		imageSrc () {
			return (this.options.image || {}).image
		},
		imageOverlayAttributes () {
			const attrs = {}
			if (this.videoSourceURL) {
				if (this.options.use_modal) {
					if (this.videoSource === 'local') {
						attrs.href = this.videoSourceURL
						attrs['data-zion-lightbox'] = true
						attrs['data-iframe'] = true
					} else {
						attrs.href = this.videoSourceURL
						attrs['data-zion-lightbox'] = true
					}
				}
			}

			return attrs
		},
		imageOverlayTag () {
			if (this.options.use_modal) {
				return 'a'
			}

			return 'div'
		},
		getImageOverlayStyles () {
			const styles = {}
			if (this.options.use_image_overlay && this.imageSrc) {
				styles['background-image'] = `url(${this.imageSrc})`
			}
			return styles
		}
	}
}
</script>
