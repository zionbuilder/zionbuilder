import {
	bulkActions
} from '@zb/rest'
import {
	applyFilters
} from '@zb/hooks'
import {
	readonly
} from 'vue'

const hash = (object) => {
	const string = JSON.stringify(object)
	let hash = 0;
	let i;
	let chr

	if (string.length === 0) return hash
	for (i = 0; i < string.length; i++) {
		chr = string.charCodeAt(i)
		hash = ((hash << 5) - hash) + chr
		hash |= 0 // Convert to 32bit integer
	}

	return hash
}

export class ServerRequest {
	constructor() {
		this.queue = []
		this.requestTimeout = 200
		this.inProgress = []
	}

	createRequester(initialData = {}) {
		initialData = applyFilters('zionbuilder/server_request/requester_data', initialData)
		return {
			request: (data, successCallback, failCallback) => {
				const parsedData = readonly({
					...initialData,
					...data
				})
				return this.request(parsedData, successCallback, failCallback)
			}
		}
	}

	request(data, successCallback, failCallback) {
		const parsedData = applyFilters('zionbuilder/server_request/data', data)
		this.addToQueue(parsedData, successCallback, failCallback)
		this.doQueue()
	}

	createCacheKey(object) {
		return hash(object)
	}

	doQueue() {
		setTimeout(() => {
			const queueItems = {}

			if (this.queue.length === 0) {
				return
			}

			this.queue.forEach((queueItem) => {
				queueItems[queueItem.key] = queueItem.data
				this.inProgress.push(queueItem)
			})

			this.queue = []

			bulkActions(queueItems).then(({
				data
			}) => {
				this.inProgress.forEach((queueItem) => {
					if (typeof queueItem['successCallback'] === 'function') {
						queueItem['successCallback'](data[queueItem.key])
					} else if (typeof queueItem['failCallback'] === 'function') {
						queueItem['failCallback']()
					}
				})
			}).catch(() => {
				// handle error
				this.inProgress.forEach((queueItem) => {
					if (typeof queueItem['failCallback'] === 'function') {
						queueItem['failCallback']()
					}
				})
			}).finally(() => {
				// Reset active queue
				this.inProgress = []
			})
		}, this.requestTimeout)
	}

	addToQueue(data, successCallback, failCallback) {
		const queueKey = this.createCacheKey({
			data,
			successCallback,
			failCallback
		})

		this.queue.push({
			key: queueKey,
			data,
			successCallback,
			failCallback
		})
	}
}
