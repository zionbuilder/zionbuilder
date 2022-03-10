let videoIndex = 0

export default class ZBVideoBg {
	constructor(domNode, options = {}) {
		this.options = {
			autoplay: true,
			muted: true,
			loop: true,
			local_sources: {},
			controlsPosition: 'bottom-left',
			controls: options.controls || true,
			videoSource: 'local',
			responsive: false,
			...options
		}

		this.domNode = domNode
		this.videoIndex = videoIndex++

		this.domNode.classList.add('hg-video-bg__wrappper')

		const videoContainer = document.createElement('div')
		videoContainer.className = 'hg-video-bg__container'
		videoContainer.id = `hg-video-bg--${this.videoIndex}`

		// Setup video
		this.videoInstance = new ZBVideo(videoContainer, {
			...this.options,
			controls: false,
			background: true
		})

		// Attach video ready actions
		this.videoInstance.on('video_ready', this.onVideoReady.bind(this))
		this.videoContainer = this.domNode.appendChild(videoContainer)
	}

	onVideoReady() {
		// Set playing state
		if (this.options.autoplay) {
			this.playing = true
			this.domNode.classList.add('hg-video-bg--playing')
		}

		// Set volume state
		if (this.options.muted) {
			this.muted = true
			this.domNode.classList.add('hg-video-bg--muted')
		}

		// Attach controls
		if (this.options.controls) {
			this.controlsWrapper = this.getControlsHTML()
			this.domNode.appendChild(this.controlsWrapper)
		}

		// Set video size for Youtube and vimeo iframe
		this.onResizeCallback = this.onWindowResize.bind(this)
		if (this.videoSource !== 'local') {
			this.initResizer()
		}
	}

	initResizer() {
		window.addEventListener('resize', this.onResizeCallback)
		// Set initial size
		this.onWindowResize()
	}

	onWindowResize() {
		const aspectRatio = 1.78
		const {
			width,
			height
		} = this.domNode.getBoundingClientRect()
		let newWidth, newHeight

		if (width === height) {
			newWidth = width * aspectRatio
			newHeight = height * aspectRatio
		} else if (width < height) {
			newWidth = height * aspectRatio
			newHeight = height
		} else {
			newWidth = width
			newHeight = width * aspectRatio
		}

		const videoContainer = this.videoInstance.getVideoContainer()

		// Set the iframe size
		videoContainer.style.width = `${newWidth}px`
		videoContainer.style.height = `${newHeight}px`
	}

	getControlsHTML() {
		const videoControlsWrapper = document.createElement('div')
		videoControlsWrapper.className = 'hg-video-bg__controls'
		videoControlsWrapper.dataset.position = this.options.controlsPosition

		const playButton = document.createElement('span')
		playButton.className = 'hg-video-bg__controls-button hg-video-bg__controls-button--play'

		const muteButton = document.createElement('span')
		muteButton.className = 'hg-video-bg__controls-button hg-video-bg__controls-button--mute'

		videoControlsWrapper.appendChild(playButton)
		videoControlsWrapper.appendChild(muteButton)

		// Attach functionality
		muteButton.addEventListener('click', this.toggleMute.bind(this))
		playButton.addEventListener('click', this.togglePlay.bind(this))

		return videoControlsWrapper
	}

	play() {
		this.videoInstance.play()
		this.playing = true
		this.domNode.classList.add('hg-video-bg--playing')
	}
	pause() {
		this.videoInstance.pause()
		this.playing = false
		this.domNode.classList.remove('hg-video-bg--playing')
	}

	togglePlay() {
		if (this.playing) {
			this.pause()
		} else {
			this.play()
		}
	}

	mute() {
		this.videoInstance.mute()
		this.muted = true
		this.domNode.classList.add('hg-video-bg--muted')
	}

	unMute() {
		this.videoInstance.unMute()
		this.muted = false
		this.domNode.classList.remove('hg-video-bg--muted')
	}

	toggleMute() {
		if (this.muted) {
			this.unMute()
		} else {
			this.mute()
		}
	}

	destroy() {
		this.videoInstance = null
		while (this.domNode.firstChild) {
			this.domNode.removeChild(this.domNode.firstChild)
		}

		window.removeEventListener('resize', this.onResizeCallback)
	}
}

// init video
jQuery('document').ready(function () {
	const $videoElements = jQuery('.zbjs_video_background')
	$videoElements.each((index, el) => {
		const $el = jQuery(el)
		const config = el.dataset.zionVideoBackground
		const options = JSON.parse(config)
		// eslint-disable-next-line no-new
		new ZBVideoBg(el, options)
		$el.zionVideoBackgroundConfig = config
	})
})