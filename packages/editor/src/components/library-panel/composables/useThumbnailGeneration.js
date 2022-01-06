let queue = []
let processing = false
const failTime = 10000 // wait 10 seconds before failing the image generation for the item

export function useThumbnailGeneration() {
	function generateScreenshot(item) {
		item.loadingThumbnail = true
		addToQueue(item)

		if (processing) {
			return
		}

		// set loading flag
		processing = true

		let iframe = generateIframe(item)
		iframe.contentWindow.addEventListener('message', onMessageReceived)

		// Set as failed if we don't get an image in 10 seconds
		const failTimeout = setTimeout(() => {
			finish(item)
		}, failTime);

		function onMessageReceived(event) {
			if (event.data && event.data.type === 'zionbuilder-screenshot') {
				const {
					success,
					data
				} = event.data

				if (success) {
					item.thumbnail = data.thumbnail
				} else {
					// Save fail to DB
					item.thumbnail_load_failed = true
				}

				// Save to DB
				item.saveThumbnailData(event.data)

				finish(item)
			}
		}

		function finish(item) {
			item.loadingThumbnail = false
			iframe.contentWindow.removeEventListener('message', onMessageReceived)
			iframe.parentNode.removeChild(iframe)
			iframe = null
			processing = false
			clearTimeout(failTimeout)

			removeFromQueue(item)

			// Process next item
			if (typeof queue[0] !== 'undefined') {
				generateScreenshot(queue[0])
			}
		}
	}

	function addToQueue(item) {
		// Remove from queue
		const itemIndex = queue.indexOf(item)
		if (itemIndex === -1) {
			queue.push(item)
		}
	}

	function removeFromQueue(item) {
		// Remove from queue
		const itemIndex = queue.indexOf(item)
		if (itemIndex !== -1) {
			queue.splice(itemIndex, 1)
		}
	}

	function generateIframe(item) {
		const iframeElement = document.createElement('iframe')
		iframeElement.src = item.screenshot_generation_url
		iframeElement.width = 1920
		iframeElement.height = 1080
		iframeElement.style = 'visibility: hidden;';

		document.body.appendChild(iframeElement)

		return iframeElement
	}

	return {
		generateScreenshot,
		removeFromQueue
	}
}
