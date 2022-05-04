import type { Player as VimeoPlayer } from '@vimeo/player';

interface Vimeo {
	Player: VimeoPlayer;
}

declare global {
	interface Window {
		onYouTubeIframeAPIReady: () => void;
		Vimeo: Vimeo;
	}
}

export interface VideoHTMLElement extends HTMLElement {
	zionVideo?: VideoOptions;
}

export interface VideoOptions {
	autoplay?: boolean;
	muted?: boolean;
	loop?: boolean;
	controls?: boolean;
	controlsPosition?: string;
	videoSource?: string;
	responsive?: boolean;
	mp4?: string;
	youtubeURL?: string;
	vimeoURL?: string;
}

let YoutubeApiLoadedState = 0;
let vimeoApiLoadedState = 0;
let videoIndex = 0;
let vimeoVolume = 1;

export default class Video {
	options: VideoOptions = {};
	domNode: VideoHTMLElement;
	videoIndex: number;
	videoContainer: HTMLElement | null = null;
	videoSource = 'local';
	YoutubeId?: string;
	player: YT.Player | HTMLVideoElement | VimeoPlayer | null;
	muted = true;
	playing = true;

	constructor(domNode: HTMLElement, options: VideoOptions = {}) {
		this.options = {
			autoplay: true,
			muted: true,
			loop: true,
			controls: true,
			controlsPosition: 'bottom-left',
			videoSource: 'local',
			responsive: true,
			...options,
		};

		// Add event bus for this instance
		this.player = null;
		this.domNode = domNode;
		this.videoIndex = videoIndex++;
		this.videoContainer = null;

		// Allow access to this instance
		this.domNode.zionVideo = this;

		// Check sources
		if (this.options.videoSource === 'local' && this.options.mp4) {
			this.videoSource = 'local';
			this.setupLocal();
		} else if (this.options.videoSource === 'youtube' && this.options.youtubeURL) {
			this.videoSource = 'youtube';
			this.YoutubeId = this.youtubeUrlParser(this.options.youtubeURL);
			this.setupYoutube();
		} else if (this.options.videoSource === 'vimeo' && this.options.vimeoURL) {
			this.videoSource = 'vimeo';
			this.setupVimeo();
		}
	}

	youtubeUrlParser(url: string) {
		const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
		const match = url.match(regExp);
		return match && match[7].length === 11 ? match[7] : undefined;
	}

	// Wait a cycle
	nextTick(callback: () => void) {
		setTimeout(() => {
			callback();
		}, 0);
	}

	setupYoutube() {
		const youtubeIframe = document.createElement('div');
		youtubeIframe.id = `znpb-video-bg-youtube-${this.videoIndex}`;

		// Append the iframe
		this.domNode.appendChild(youtubeIframe);

		if (YoutubeApiLoadedtSate === 0) {
			// 2. This code loads the IFrame Player API code asynchronously.
			const youtubeTag = document.createElement('script');
			youtubeTag.src = 'https://www.youtube.com/iframe_api';
			const firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode?.insertBefore(youtubeTag, firstScriptTag);

			window.onYouTubeIframeAPIReady = () => {
				this.enableYoutube();
				// trigger event
				globalEventBus.trigger('youtube_api_ready');
				YoutubeApiLoadedState = 2;
			};
			YoutubeApiLoadedState = 1;
		} else if (YoutubeApiLoadedState === 1) {
			globalEventBus.on('youtube_api_ready', this.enableYoutube.bind(this));
		} else if (YoutubeApiLoadedState === 2) {
			this.enableYoutube();
		}
	}

	enableYoutube() {
		this.player = new window.YT.Player(`znpb-video-bg-youtube-${this.videoIndex}`, {
			height: '100%',
			width: '100%',
			videoId: this.YoutubeId,
			playerVars: {
				mute: this.options.muted ? 1 : 0,
				autoplay: this.options.autoplay ? 1 : 0,
				iv_load_policy: 3,
				showinfo: 0,
				controls: this.options.controls ? 1 : 0,
				modestbranding: 1,
				rel: 0,
				origin: window.location.hostname,
			},
		});

		this.videoContainer = this.player.getIframe();
	}

	setupVimeo() {
		const vimeoContainer = document.createElement('div');
		vimeoContainer.id = `znpb-video-bg-vimeo-${this.videoIndex}`;
		this.domNode.appendChild(vimeoContainer);

		if (vimeoApiLoadedState === 0) {
			const vimeoTag = document.createElement('script');
			vimeoTag.src = 'https://player.vimeo.com/api/player.js';
			const secondScriptTag = document.getElementsByTagName('script')[1];
			secondScriptTag.parentNode?.insertBefore(vimeoTag, secondScriptTag);

			vimeoTag.onload = () => {
				this.enableVimeo();
				globalEventBus.trigger('vimeo_api_ready');
				vimeoApiLoadedState = 2;
			};
			vimeoApiLoadedState = 1;
		} else if (vimeoApiLoadedState === 1) {
			globalEventBus.on('vimeo_api_ready', this.enableVimeo.bind(this));
		} else if (vimeoApiLoadedState === 2) {
			this.enableVimeo();
		}
	}

	enableVimeo() {
		this.player = new window.Vimeo.Player(`znpb-video-bg-vimeo-${this.videoIndex}`, {
			id: this.options.vimeoURL,
			background: this.options.autoplay,
			muted: this.options.muted,
			transparent: true,
			autoplay: this.options.autoplay,
			controls: this.options.controls,
		});

		(this.player as VimeoPlayer).ready().then(() => {
			this.videoContainer = (this.player as VimeoPlayer).element;
		});
	}

	setupLocal() {
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

		this.domNode.appendChild(videoElement);

		this.player = videoElement;
		this.videoContainer = videoElement;
	}

	getVideoContainer() {
		return this.videoContainer;
	}

	play() {
		if (this.videoSource === 'youtube') {
			(this.player as YT.Player).playVideo();
		}
		if (this.videoSource === 'vimeo') {
			(this.player as VimeoPlayer).play();
		}
		if (this.videoSource === 'local') {
			(this.player as HTMLVideoElement).play();
		}
		this.playing = true;
	}

	pause() {
		if (this.videoSource === 'youtube') {
			(this.player as YT.Player).pauseVideo();
		}
		if (this.videoSource === 'vimeo') {
			(this.player as VimeoPlayer).pause();
		}
		if (this.videoSource === 'local') {
			(this.player as HTMLVideoElement).pause();
		}
		this.playing = false;
	}

	togglePlay() {
		if (this.playing) {
			this.pause();
		} else {
			this.play();
		}
	}

	mute() {
		if (this.videoSource === 'youtube') {
			(this.player as YT.Player).mute();
		}
		if (this.videoSource === 'vimeo') {
			(this.player as VimeoPlayer).getVolume().then((volume: number) => {
				vimeoVolume = volume;
			});
			(this.player as VimeoPlayer).setVolume(0);
		}
		if (this.videoSource === 'local') {
			(this.player as HTMLVideoElement).muted = true;
		}

		this.muted = true;
	}

	unMute() {
		if (this.videoSource === 'youtube') {
			(this.player as YT.Player).unMute();
		}
		if (this.videoSource === 'vimeo') {
			(this.player as VimeoPlayer).setVolume(vimeoVolume);
		}
		if (this.videoSource === 'local') {
			(this.player as HTMLVideoElement).muted = false;
		}

		this.muted = false;
	}

	toggleMute() {
		if (this.muted) {
			this.unMute();
		} else {
			this.mute();
		}
	}

	destroy() {
		this.player = null;
		while (this.domNode.firstChild) {
			this.domNode.removeChild(this.domNode.firstChild);
		}
	}
}
