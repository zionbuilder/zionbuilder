import {
	bulkActions
} from '@zb/rest'
import {
	applyFilters
} from '@zb/hooks'
import {
	hash
} from '@zb/utils'

export class ServerRequest {
	constructor() {
		this.queue = []
		this.requestTimeout = 200
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

	request(data, successCallback, failCallback) {
		const parsedData = applyFilters('zionbuilder/server_request/data', data)
		const rawData = JSON.parse(JSON.stringify(parsedData))

		// Check to see if we actually need to look into the cache
		const cacheKey = this.createCacheKey(rawData)

		if (data.useCache && this.isCached(cacheKey)) {
			successCallback(this.getFromCache(cacheKey))
		} else {
			this.addToQueue(rawData, successCallback, failCallback)
			this.doQueue()
		}
	}

	createCacheKey(object) {
		return hash(object)
	}

	doQueue() {
		setTimeout(() => {
			const queueItems = {}
			const inProgress = []

			if (this.queue.length === 0) {
				return
			}

			this.queue.forEach((queueItem) => {
				queueItems[queueItem.key] = queueItem.data
				inProgress.push(queueItem)
			})

			this.queue = []

			bulkActions(queueItems).then(({
				data
			}) => {
				inProgress.forEach((queueItem) => {
					if (typeof queueItem['successCallback'] === 'function') {
						// Save to cache
						const {
							useCache
						} = queueItem.data

						if (useCache) {
							this.addToCache(queueItem.key, data[queueItem.key])
						}

						// Send the response
						queueItem['successCallback'](data[queueItem.key])
					}
				})
			}).catch(() => {
				// handle error
				inProgress.forEach((queueItem) => {
					if (typeof queueItem['failCallback'] === 'function') {
						queueItem['failCallback']()
					}
				})
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
