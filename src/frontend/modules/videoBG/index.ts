import ZBVideo from '../video';

let videoIndex = 0;

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
			...options,
		};

		this.domNode = domNode;
		this.videoIndex = videoIndex++;

		this.domNode.classList.add('hg-video-bg__wrappper');

		const videoContainer = document.createElement('div');
		videoContainer.className = 'hg-video-bg__container';
		videoContainer.id = `hg-video-bg--${this.videoIndex}`;

		// Setup video
		this.videoInstance = new ZBVideo(videoContainer, {
			...this.options,
			controls: false,
			background: true,
			playsInline: true,
		});

		// Attach video ready actions
		this.videoInstance.on('video_ready', this.onVideoReady.bind(this));
		this.videoContainer = this.domNode.appendChild(videoContainer);
	}

	onVideoReady() {
		// Set playing state
		if (this.options.autoplay) {
			this.playing = true;
			this.domNode.classList.add('hg-video-bg--playing');
		}

		// Set volume state
		if (this.options.muted) {
			this.muted = true;
			this.domNode.classList.add('hg-video-bg--muted');
		}

		// Attach controls
		if (this.options.controls) {
			this.controlsWrapper = this.getControlsHTML();
			this.domNode.appendChild(this.controlsWrapper);
		}

		// Set video size for Youtube and vimeo iframe
		this.onResizeCallback = this.onWindowResize.bind(this);
		if (this.videoSource !== 'local') {
			this.initResizer();
		}
	}

	initResizer() {
		window.addEventListener('resize', this.onResizeCallback);
		// Set initial size
		this.onWindowResize();
	}

	onWindowResize() {
		const aspectRatio = 1.78;
		const { width, height } = this.domNode.getBoundingClientRect();
		let newWidth, newHeight;

		if (width === height) {
			newWidth = width * aspectRatio;
			newHeight = height * aspectRatio;
		} else if (width < height) {
			newWidth = height * aspectRatio;
			newHeight = height;
		} else {
			newWidth = width;
			newHeight = width * aspectRatio;
		}

		const videoContainer = this.videoInstance.getVideoContainer();

		// Set the iframe size
		videoContainer.style.width = `${newWidth}px`;
		videoContainer.style.height = `${newHeight}px`;
	}

	getControlsHTML() {
		const videoControlsWrapper = document.createElement('div');
		videoControlsWrapper.className = 'hg-video-bg__controls';
		videoControlsWrapper.dataset.position = this.options.controlsPosition;

		const playButton = document.createElement('span');
		playButton.className = 'hg-video-bg__controls-button hg-video-bg__controls-button--play';
		playButton.innerHTML = `
			<svg class="zb-icon hg-video-bg__controls-button--svg-play" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M15.1 16.3 42.5 32 15.1 47.7V16.3M8.7 5.1v53.8L55.3 32 8.7 5.1z"/></svg>
			<svg class="zb-icon hg-video-bg__controls-button--svg-pause" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M11.5 10.9h10.2v42.2H11.5V10.9zm30.7 0h10.2v42.2H42.2V10.9z"/></svg>
		`;

		const muteButton = document.createElement('span');
		muteButton.className = 'hg-video-bg__controls-button hg-video-bg__controls-button--mute';
		muteButton.innerHTML = `
			<svg class="zb-icon hg-video-bg__controls-button--svg-mute" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M24.7 16.3v31.6l-10.1-5.8-1.5-.9H6.4V22.9h6.7l1.5-.9 10.1-5.7m6.4-11.2L11.4 16.5H0v31h11.4l19.7 11.4V5.1zM64 23.8l-4.5-4.5-8.2 8.2-8.2-8.2-4.5 4.5 8.1 8.2-8.1 8.2 4.5 4.5 8.2-8.2 8.2 8.2 4.5-4.5-8.2-8.2 8.2-8.2z"/></svg>
			<svg class="zb-icon hg-video-bg__controls-button--svg-unmute" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M24.7 16.3v31.6l-10.1-5.8-1.6-.9H6.4V22.9H13l1.5-.9 10.2-5.7m6.4-11.2L11.4 16.5H0v31h11.4l19.7 11.4V5.1zM53 58.6l-4.5-4.5c12.2-12.2 12.2-32 0-44.1L53 5.5c14.7 14.6 14.7 38.4 0 53.1zm-10.5-8.7L38 45.4c7.4-7.4 7.4-19.3 0-26.7l4.5-4.5c9.8 9.7 9.8 25.9 0 35.7z"/></svg>
		`;

		videoControlsWrapper.appendChild(playButton);
		videoControlsWrapper.appendChild(muteButton);

		// Attach functionality
		muteButton.addEventListener('click', this.toggleMute.bind(this));
		playButton.addEventListener('click', this.togglePlay.bind(this));

		return videoControlsWrapper;
	}

	play() {
		this.videoInstance.play();
		this.playing = true;
		this.domNode.classList.add('hg-video-bg--playing');
	}
	pause() {
		this.videoInstance.pause();
		this.playing = false;
		this.domNode.classList.remove('hg-video-bg--playing');
	}

	togglePlay() {
		if (this.playing) {
			this.pause();
		} else {
			this.play();
		}
	}

	mute() {
		this.videoInstance.mute();
		this.muted = true;
		this.domNode.classList.add('hg-video-bg--muted');
	}

	unMute() {
		this.videoInstance.unMute();
		this.muted = false;
		this.domNode.classList.remove('hg-video-bg--muted');
	}

	toggleMute() {
		if (this.muted) {
			this.unMute();
		} else {
			this.mute();
		}
	}

	destroy() {
		this.videoInstance = null;
		while (this.domNode.firstChild) {
			this.domNode.removeChild(this.domNode.firstChild);
		}

		window.removeEventListener('resize', this.onResizeCallback);
	}
}

// init video
const elements = Array.from(document.querySelectorAll('.zbjs_video_background'));
if (elements.length) {
	elements.forEach(el => {
		const config = el.dataset.zionVideoBackground;
		const options = JSON.parse(config);
		// eslint-disable-next-line no-new
		new ZBVideoBg(el, options);
		el.zionVideoBackgroundConfig = config;
	});
}
window.ZBVideoBg = ZBVideoBg;
