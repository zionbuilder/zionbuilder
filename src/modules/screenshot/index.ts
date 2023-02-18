import './scss/index.scss';
import { toPng } from 'html-to-image';

export default function screenshot() {
	const options = window.ZnPbScreenshotData;

	function init() {
		// Fix iframe as the html-to-image cannot screenshot iframe content
		handleIframe();

		// Proxy external assets
		handleCSS();

		// Proxy external images
		handleImages();

		// create the image
		window.addEventListener('load', createImage);
	}

	function createImage() {
		// Take screenshot
		toPng(document.body)
			.then(dataUrl => {
				sendMessage({
					success: true,
					thumbnail: dataUrl,
				});

				// download(dataUrl)
			})
			.catch(error => {
				sendMessage({
					success: false,
					errorMessage: error,
				});
			});
	}

	function handleCSS() {
		const stylesheets = document.getElementsByTagName('link');

		const externalStylesheets = Array.from(stylesheets).filter(stylesheet => {
			return stylesheet.rel === 'stylesheet' && stylesheet.href.indexOf(options.home_url) === -1;
		});

		for (const stylesheet of externalStylesheets) {
			stylesheet.href = getProxyURL(stylesheet.href);
		}
	}

	function getProxyURL(url: string) {
		return `${options.home_url}?${options.constants.PROXY_URL_ARGUMENT}&${options.constants.PROXY_URL_NONCE_KEY}=${options.nonce_key}&${options.constants.PROXY_ASSET_PARAM}=${url}`;
	}

	function handleImages() {
		const images = document.getElementsByTagName('img');

		const externalImages = Array.from(images).filter(image => {
			return image.src.indexOf(options.home_url) === -1;
		});

		for (const image of externalImages) {
			image.src = getProxyURL(image.src);
		}
	}

	function handleIframe() {
		const iframe = document.getElementsByTagName('iframe');
		Array.from(iframe).forEach(iframe => {
			const { width, height } = iframe.getBoundingClientRect();

			// Create div element
			const iframePlaceholder = document.createElement('div');
			iframePlaceholder.style.width = `${width}px`;
			iframePlaceholder.style.height = `${height}px`;
			iframePlaceholder.classList.add('znpb-iframePlaceholderBG');

			iframe.parentElement?.replaceChild(iframePlaceholder, iframe);
		});
	}

	function sendMessage(message: { success: boolean; error?: unknown; thumbnail?: string; errorMessage?: string }) {
		window.postMessage({
			type: 'zionbuilder-screenshot',
			...message,
		});
	}

	try {
		// Take the screenshot
		init();
	} catch (error) {
		console.error(error);
		sendMessage({
			success: false,
			error,
		});
	}
}

screenshot();
