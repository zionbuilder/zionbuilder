require('./scss/index.scss')
import { toPng } from "html-to-image";

export default function screenshot() {
	const options = ZnPbScreenshootData

	function init() {
		// Fix iframes as the html-to-image cannot screenshot iframe content
		handleIframes()

		// Proxy external assets
		handleCSS()

		// Proxy external images
		handleImages()

		// create the image
		window.addEventListener('load', createImage)
	}

	function createImage() {
		// Take screenshoot
		toPng(document.body)
			.then((dataUrl) => {
				sendMessage({
					success: true,
					thumbnail: dataUrl
				})

				// download(dataUrl)
			})
			.catch((error) => {
				sendMessage({
					success: false,
					errorMessage: error
				})
			})
	}

	function handleCSS() {
		const stylesheets = document.getElementsByTagName('link')

		const externallStylesheets = Array.from(stylesheets).filter(stylesheet => {
			return stylesheet.rel === 'stylesheet' && stylesheet.href.indexOf(options.home_url) === -1
		})

		for (const stylesheet of externallStylesheets) {
			stylesheet.href = getProxyURL(stylesheet.href)
		}
	}

	function getProxyURL(url: string) {
		return `${options.home_url}?${options.constants.PROXY_URL_ARGUMENT}&${options.constants.PROXY_URL_NONCE_KEY}=${options.nonce_key}&${options.constants.PROXY_ASSET_PARAM}=${url}`
	}

	function handleImages() {
		const images = document.getElementsByTagName('img')

		const externalImages = Array.from(images).filter(image => {
			return image.src.indexOf(options.home_url) === -1
		})

		for (const image of externalImages) {
			image.src = getProxyURL(image.src)
		}

	}

	function handleIframes() {
		const iframes = document.getElementsByTagName('iframe')
		Array.from(iframes).forEach(iframe => {
			const { width, height } = iframe.getBoundingClientRect()

			// Create div element
			const iframePlaceholder = document.createElement('div')
			iframePlaceholder.style.width = `${width}px`
			iframePlaceholder.style.height = `${height}px`
			iframePlaceholder.classList.add('znpb-iframePlaceholderBG')

			iframe.parentElement?.replaceChild(iframePlaceholder, iframe)
		})
	}

	function download(url: string) {
		const link = document.createElement('a')
		link.href = url
		link.download = 'image.png'

		const img = document.createElement('img')
		img.src = url
		document.body.append(img)

		document.body.append(link)
		link.click()
	}

	function sendMessage(message) {
		window.postMessage({
			type: 'zionbuilder-screenshot',
			...message
		})
	}

	try {
		// Take the screenshot
		init()
	} catch (error) {
		console.error(error)
		sendMessage({
			success: false,
			error
		})
	}

}

screenshot()
