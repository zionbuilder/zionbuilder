import './scss/main.scss'

(function ($) {
	window.ZionBuilderFrontend.registerScript('video', {
		run (scope) {
			const $element = $(scope).find('.zb-el-zionVideo').addBack('.zb-el-zionVideo')

			if ($element.length > 0) {
				$element.each((i, el) => {
					this.initVideo(el)
				})
			}
		},
		initVideo (el) {
			let videoPlayer

			const $el = $(el)
			const $overlay = $('.zb-el-zionVideo-overlay', $el)
			const $videoWrapper = $('.zb-el-zionVideo-wrapper', $el)
			const elementConfig = JSON.parse($el.attr('data-zion-video'))

			const onOverlayClick = function () {
				$overlay.fadeOut()
				videoPlayer.play()
			}

			if (elementConfig) {
				const videoConfig = elementConfig.video_config

				if (elementConfig.use_image_overlay && !elementConfig.use_modal) {
					videoConfig.autoplay = false
					$overlay.on('click', onOverlayClick)
				}

				videoPlayer = new ZBVideo($videoWrapper[0], videoConfig)

				videoPlayer.on('beforeDestroy', function () {
					$overlay.off('click', onOverlayClick)
				})

				return videoPlayer
			}
		}
	})
})(window.jQuery)
