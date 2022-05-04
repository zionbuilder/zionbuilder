import './scss/main.scss';

export interface VideoOptions {
	autoplay?: boolean;
	muted?: boolean;
	loop?: boolean;
	controls?: boolean;
	controlsPosition?: string;
	videoSource?: string;
	responsive?: boolean;
	use_image_overlay: boolean;
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
		}
	}

	// Pause the video
	pause() {
		if (this.youtubePlayer) {
			this.youtubePlayer.pauseVideo();
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
			// backdrop.parentElement?.removeChild(backdrop);
		});
	}

	// Initialize the video
	initVideo() {
		if (this.isInit) {
			return;
		}

		if (this.options?.video_config?.videoSource === 'youtube') {
			this.initYoutube();
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
