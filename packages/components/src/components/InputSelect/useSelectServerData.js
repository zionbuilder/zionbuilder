import {
	ref,
	toRaw
} from 'vue'
import hash from 'object-hash'
import {
	set,
	get,
	unionBy
} from 'lodash-es'

const items = ref([])
const cache = ref({})

export function useSelectServerData(config) {
	let requester
	if (window.zb.editor) {
		requester = window.zb.editor.serverRequest
	} else if (window.zb.admin.serverRequest) {
		requester = window.zb.admin.serverRequest
	}

	function fetch(config) {
		const cacheKey = generateCacheKey(toRaw(config))

		if (cache[cacheKey]) {
			return Promise.resolve(cache[cacheKey])
		} else {
			return new Promise((resolve, reject) => {
				requester.request({
					type: 'get_input_select_options',
					config: config
				}, (response) => {
					// Save the new items
					saveItems(config.server_callback_method, response.data)

					// add items to cache
					addToCache(cacheKey, response.data)

					// Send back the response in case it is needed
					resolve(response.data)
				}, function (message) {
					reject(message)
				})
			})
		}
	}

	function getItems(server_callback_method) {
		return get(items.value, server_callback_method, [])
	}

	function getItem(server_callback_method, id) {
		const cachedItems = get(items.value, server_callback_method, [])
		return cachedItems.find(item => item.id === id)
	}

	function generateCacheKey(data) {
		const {
			server_callback_method,
			page,
			searchKeyword,
			...remainingProperties
		} = data

		return hash({
			server_callback_method,
			page,
			searchKeyword,
		})
	}

	function saveItems(server_callback_method, newItems) {
		const existingItems = get(items.value, server_callback_method, [])
		items.value[server_callback_method] = unionBy(existingItems, newItems, 'id')
	}

	function addToCache(cacheKey, cacheData) {
		cache[cacheKey] = cacheData
	}

	return {
		fetch,
		getItem,
		getItems
	}
}
