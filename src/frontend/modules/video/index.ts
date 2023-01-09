import { createHooksInstance } from '/@/common/modules/hooks/hooks';
import reframe from 'reframe.js';

declare global {
	interface Window {
		onYouTubeIframeAPIReady: any;
		YT: any;
		Vimeo: any;
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
	playsInline?: boolean | null;
}

let YoutubeApiLoadedState = 0;
let vimeoApiLoadedState = 0;
let videoIndex = 0;
let vimeoVolume = 1;

const globalEventBus = createHooksInstance();

export default class Video {
	options: VideoOptions = {};
	eventBus;
	on;
	off;
	trigger;
	domNode: VideoHTMLElement;
	videoIndex: number;
	videoContainer: HTMLElement | null = null;
	videoSource = 'local';
	YoutubeId?: string;
	player: any;
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
			playsInline: null,
			...options,
		};

		// Add event bus for this instance
		this.eventBus = createHooksInstance();
		this.on = this.eventBus.addAction;
		this.off = this.eventBus.removeAction;
		this.trigger = this.eventBus.doAction;

		this.domNode = domNode;
		this.videoIndex = videoIndex++;
		this.videoContainer = null;

		if (this.options.responsive) {
			this.on('video_ready', () => {
				reframe(this.videoContainer);
			});
		}

		// Allow access to this instance
		this.domNode.zionVideo = this;

		this.nextTick(() => {
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
		});
	}

	youtubeUrlParser(url) {
		const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
		const match = url.match(regExp);
		return match && match[7].length === 11 ? match[7] : false;
	}

	// Wait a cycle
	nextTick(callback: () => void) {
		setTimeout(() => {
			callback();
		}, 0);
	}

	setupYoutube() {
		const YtParams = {
			mute: this.options.muted ? 1 : 0,
			autoplay: this.options.autoplay ? 1 : 0,
			iv_load_policy: 3,
			showinfo: 0,
			controls: this.options.controls ? 1 : 0,
			modestbranding: 1,
			rel: 0,
			wmode: 'transparent',
		};

		let YtParamsString = '';
		for (const [key, value] of Object.entries(YtParams)) {
			YtParamsString += `&${key}=${value}`;
		}

		const youtubeIframe = document.createElement('iframe');
		youtubeIframe.src = `https://www.youtube-nocookie.com/embed/${this.YoutubeId}?enablejsapi=1${YtParamsString}`;
		youtubeIframe.id = `znpb-video-bg-youtube-${this.videoIndex}`;
		youtubeIframe.allow = 'autoplay; fullscreen';
		youtubeIframe.width = 425;
		youtubeIframe.height = 239;

		// Append the iframe
		this.domNode.appendChild(youtubeIframe);

		if (YoutubeApiLoadedState === 0) {
			// 2. This code loads the IFrame Player API code asynchronously.
			const youtubeTag = document.createElement('script');
			youtubeTag.src = 'https://www.youtube.com/iframe_api';
			const firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(youtubeTag, firstScriptTag);

			window.onYouTubeIframeAPIReady = () => {
				this.enableYoutube();
				// trigger event
				globalEventBus.doAction('youtube_api_ready');
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
		});

		this.videoContainer = this.player.getIframe();

		this.trigger('video_ready');
	}

	setupVimeo() {
		const vimeoContainer = document.createElement('div');
		vimeoContainer.id = `znpb-video-bg-vimeo-${this.videoIndex}`;
		this.domNode.appendChild(vimeoContainer);

		if (vimeoApiLoadedState === 0) {
			const vimeoTag = document.createElement('script');
			vimeoTag.src = 'https://player.vimeo.com/api/player.js';
			const secondScriptTag = document.getElementsByTagName('script')[1];
			const self = this;
			secondScriptTag.parentNode.insertBefore(vimeoTag, secondScriptTag);

			vimeoTag.onload = function () {
				self.enableVimeo();
				globalEventBus.doAction('vimeo_api_ready');
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

		this.player.ready().then(() => {
			this.videoContainer = this.player.element;
			this.trigger('video_ready');
		});
	}

	setupLocal() {
		const loop = this.options.loop ? 'loop' : '';

		const videoElement = document.createElement('video');

		// Set video arguments
		videoElement.setAttribute('muted', '');

		if (this.options.autoplay) {
			videoElement.setAttribute('autoplay', 'true');
		}

		videoElement.setAttribute('loop', loop);

		if (this.options.controls) {
			videoElement.controls = true;
		}

		if (typeof this.options.playsInline !== 'undefined' && this.options.playsInline !== null) {
			videoElement.setAttribute('playsInline', '' + this.options.playsInline);
		}

		if (this.options.mp4) {
			const sourceMP4 = document.createElement('source');
			sourceMP4.src = this.options.mp4;
			videoElement.appendChild(sourceMP4);
		}

		this.domNode.appendChild(videoElement);

		this.player = videoElement;
		this.videoContainer = videoElement;

		this.trigger('video_ready');
	}

	getVideoContainer() {
		return this.videoContainer;
	}

	play() {
		if (this.videoSource === 'youtube') {
			this.player.playVideo();
		}
		if (this.videoSource === 'vimeo') {
			this.player.play();
		}
		if (this.videoSource === 'local') {
			this.player.play();
		}
		this.playing = true;
	}

	pause() {
		if (this.videoSource === 'youtube') {
			this.player.pauseVideo();
		}
		if (this.videoSource === 'vimeo') {
			this.player.pause();
		}
		if (this.videoSource === 'local') {
			this.player.pause();
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
			this.player.mute();
		}
		if (this.videoSource === 'vimeo') {
			this.player.getVolume().then((volume: number) => {
				vimeoVolume = volume;
			});
			this.player.setVolume(0);
		}
		if (this.videoSource === 'local') {
			this.player.muted = true;
		}

		this.muted = true;
	}

	unMute() {
		if (this.videoSource === 'youtube') {
			this.player.unMute();
		}
		if (this.videoSource === 'vimeo') {
			this.player.setVolume(vimeoVolume);
		}
		if (this.videoSource === 'local') {
			this.player.muted = false;
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
		this.trigger('beforeDestroy');
		this.player = null;
		while (this.domNode.firstChild) {
			this.domNode.removeChild(this.domNode.firstChild);
		}
	}
}

window.zbVideo = Video;
