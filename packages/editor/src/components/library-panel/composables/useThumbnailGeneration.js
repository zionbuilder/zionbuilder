import {
	find
} from 'lodash-es'

let queue = []
let processing = false
let iframeElement

export function useThumbnailGeneration() {
	function addToQueue(item) {
		if (!find(queue, {
				id: item.id
			})) {
			queue.push(item)
		}

		return this
	}

	function generateIframe(item) {
		iframeElement = document.createElement('iframe')
		iframeElement.src = item.screenshot_generation_url

		document.body.appendChild(iframeElement)
	}

	function cleanupIframe() {
		// Cleanup
		if (iframeElement) {
			iframeElement.removeEventListener('message', onMessageReceived)
			iframeElement.parentNode.removeChild(iframeElement)
		}
	}

	function attachEvents() {
		iframeElement.addEventListener('message', onMessageReceived)
	}

	function onMessageReceived(event) {
		console.log({
			event
		})
	}

	function processQueue() {
		if (processing) {
			return
		}

		if (typeof queue[0] !== 'undefined') {
			generateIframe(queue[0])
			attachEvents()

		}
	}

	function next() {
		if (typeof queue[0] !== 'undefined') {
			generateIframe()
		}
	}

	function resetQueue() {
		queue = []


	}

	return {
		addToQueue,
		generateScreenshot,
		processQueue,
		resetQueue
	}
}
