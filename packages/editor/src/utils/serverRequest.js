import {
	bulkActions
} from '@zb/rest'
import {
	applyFilters
} from '@zb/hooks'
import {
	readonly
} from 'vue'
import hash from 'object-hash'


export class ServerRequest {
	constructor() {
		this.queue = []
		this.requestTimeout = 200
		this.inProgress = []
		this.cache = {}
	}

	addToCache(id, result) {
		this.cache[id] = result
	}

	getFromCache(cacheKey) {
		return this.cache[cacheKey]
	}

	isCached(cacheKey) {
		return typeof this.cache[cacheKey] !== 'undefined'
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
		// Check to see if we actually need to look into the cache
		const cacheKey = this.createCacheKey(data)

		if (data.useCache && this.isCached(cacheKey)) {
			successCallback(this.getFromCache(cacheKey))
			// return this.getFromCache(cacheKey)
		} else {
			this.addToQueue(parsedData, successCallback, failCallback)
			this.doQueue()
		}
	}

	createCacheKey(object) {
		// const clone = JSON.parse(JSON.stringify(object))
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
						// Save to cache
						this.addToCache(queueItem.key, data[queueItem.key])

						// Send the response
						queueItem['successCallback'](data[queueItem.key])
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
		const queueKey = this.createCacheKey(data)

		this.queue.push({
			key: queueKey,
			data,
			successCallback,
			failCallback
		})
	}
}
