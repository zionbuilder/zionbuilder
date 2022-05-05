import './scss/main.scss';

// TODO: add extra options for specific video sources
// TODO: add lazy loading

export interface VideoOptions {
	autoplay?: boolean;
	muted?: boolean;
	loop?: boolean;
	controls?: boolean;
	controlsPosition?: string;
	videoSource?: string;
	responsive?: boolean;
	use_image_overlay: boolean;
	// HTML5 related
	mp4: string;
	// Vimeo related
	vimeoURL: string;
	video_config?: {
		youtubeURL?: string;
		vimeoURL?: string;
		mp4?: string;
		videoSource?: string;
	};
}

class Video {
	options: VideoOptions;
	domNode?: HTMLElement;
	youtubePlayer?: YT.Player;
	vimeoPlayer?;
	html5Player?: HTMLVideoElement;
	isInit = false;

	constructor(domNode: HTMLElement) {
		this.domNode = domNode;
		this.options = this.getConfig();

		// Check to see if we are inside a modal
		const modalParent = this.domNode.closest('.zb-modal');

		if (modalParent) {
			modalParent.addEventListener('openModal', () => {
				if (this.isInit) {
					this.play();
				} else {
					this.init();
				}
			});

			modalParent.addEventListener('closeModal', () => {
				this.pause();
			});
		} else {
			this.init();
		}
	}

	// Plays the video
	play() {
		if (this.youtubePlayer) {
			this.youtubePlayer.playVideo();
		} else if (this.html5Player) {
			this.html5Player.play();
		} else if (this.vimeoPlayer) {
			this.vimeoPlayer.play();
		}
	}

	// Pause the video
	pause() {
		if (this.youtubePlayer) {
			this.youtubePlayer.pauseVideo();
		} else if (this.html5Player) {
			this.html5Player.pause();
		} else if (this.vimeoPlayer) {
			this.vimeoPlayer.pause();
		}
	}

	init() {
		if (this.options.use_image_overlay) {
			this.initBackdrop();
		} else {
			this.initVideo();
		}
	}

	initBackdrop() {
		const backdrop = this.domNode?.querySelector('.zb-el-zionVideo-overlay');
		backdrop?.addEventListener('click', () => {
			this.initVideo();
			backdrop.parentElement?.removeChild(backdrop);
		});
	}

	// Initialize the video
	initVideo() {
		if (this.isInit) {
			return;
		}

		if (this.options?.video_config?.videoSource === 'youtube') {
			this.initYoutube();
		} else if (this.options?.video_config?.videoSource === 'local') {
			this.initHTML5();
		} else if (this.options?.video_config?.videoSource === 'vimeo') {
			this.initVimeo();
		}

		this.isInit = true;
	}

	getYoutubeVideoID(url: string) {
		const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
		const match = url.match(regExp);
		return match && match[7].length === 11 ? match[7] : undefined;
	}

	onYoutubeAPIReady(callback) {
		if (window.YT && window.YT.Player) {
			callback(window.YT.Player);
			return;
		} else if (!window.ZbAttachedYoutubeScript) {
			this.attachYoutubeScript();
		}

		setTimeout(() => {
			this.onYoutubeAPIReady(callback);
		}, 200);
	}

	attachYoutubeScript() {
		if (window.ZbAttachedYoutubeScript) {
			return;
		}

		const tag = document.createElement('script');

		tag.src = 'https://www.youtube.com/iframe_api';
		const firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag?.parentNode?.insertBefore(tag, firstScriptTag);

		window.ZbAttachedYoutubeScript = true;
	}

	initYoutube() {
		if (!this.options.video_config?.youtubeURL) {
			return;
		}

		// get the video id
		const videoID = this.getYoutubeVideoID(this.options.video_config?.youtubeURL);

		if (!videoID) {
			return;
		}

		// Create iframe
		const iframe = document.createElement('div');
		this.domNode?.appendChild(iframe);

		this.onYoutubeAPIReady(() => {
			this.youtubePlayer = new window.YT.Player(iframe, {
				videoId: videoID,
				playerVars: {
					autoplay: this.options.autoplay ? 1 : 0,
					controls: this.options.controls ? 1 : 0,
					mute: this.options.muted ? 1 : 0,
					playsinline: 1,
					modestbranding: 1,
					origin: window.location.host,
				},
				host: 'https://www.youtube-nocookie.com',
			});
		});
	}

	onVimeoApiReady(callback) {
		if (window.Vimeo && window.Vimeo.Player) {
			callback();
			return;
		} else if (!window.ZbAttachedVimeoScript) {
			this.attachVimeoScript();
		}

		setTimeout(() => {
			this.onVimeoApiReady(callback);
		}, 200);
	}

	attachVimeoScript() {
		if (window.ZbAttachedVimeoScript) {
			return;
		}

		const tag = document.createElement('script');

		tag.src = 'https://player.vimeo.com/api/player.js';
		const firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag?.parentNode?.insertBefore(tag, firstScriptTag);

		window.ZbAttachedVimeoScript = true;
	}

	initVimeo() {
		if (!this.options.video_config?.vimeoURL) {
			return;
		}

		// Create video container
		const videoContainer = document.createElement('div');
		this.domNode?.appendChild(videoContainer);

		this.onVimeoApiReady(() => {
			this.vimeoPlayer = new window.Vimeo.Player(videoContainer, {
				id: this.options?.vimeoURL,
				background: false,
				muted: this.options.muted,
				transparent: true,
				autoplay: this.options.autoplay,
				controls: this.options.controls,
			});
		});
	}

	// Init HTML5 Video
	initHTML5() {
		const autoplay = this.options.autoplay ? true : false;
		const muted = this.options.muted ? true : false;
		const loop = this.options.loop ? true : false;

		const videoElement = document.createElement('video');

		// Set video arguments
		videoElement.muted = muted;
		videoElement.autoplay = autoplay;
		videoElement.loop = loop;

		if (this.options.controls) {
			videoElement.controls = true;
		}

		if (this.options.mp4) {
			const sourceMP4 = document.createElement('source');
			sourceMP4.src = this.options.mp4;
			videoElement.appendChild(sourceMP4);
		}

		this.domNode?.appendChild(videoElement);

		this.html5Player = videoElement;
	}

	getConfig() {
		const configAttr = this.domNode?.dataset.zionVideo;
		const options = configAttr ? JSON.parse(configAttr) : {};

		return {
			autoplay: true,
			muted: true,
			loop: true,
			controls: true,
			controlsPosition: 'bottom-left',
			videoSource: 'local',
			responsive: true,
			...options,
			...(options.video_config || {}),
		};
	}
}

// Init the script
document.querySelectorAll('.zb-el-zionVideo').forEach(domNode => {
	new Video(<HTMLElement>domNode);
});

window.zbScripts = window.zbScripts || {};
window.zbScripts.video = Video;
