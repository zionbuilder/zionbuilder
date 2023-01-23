import { ref, toRaw, inject } from 'vue';

import { hash } from '../../utils';
import { get, unionBy } from 'lodash-es';

const cache = ref({});

export function useSelectServerData() {
	let requester = inject('serverRequester', null);

	const items = ref([]);

	if (!requester) {
		if (window.zb.admin) {
			requester = window.zb.admin.serverRequest;
		}
	}

	function fetch(config) {
		if (!requester) {
			return Promise.reject('Server requester not provided');
		}

		const cacheKey = generateCacheKey(toRaw(config));
		const saveItemsCache = generateItemsCacheKey(toRaw(config));

		if (cache[cacheKey]) {
			saveItems(saveItemsCache, cache[cacheKey]);
			return Promise.resolve(cache[cacheKey]);
		} else {
			return new Promise((resolve, reject) => {
				// Allow value caching
				config.useCache = true;

				requester.request(
					{
						type: 'get_input_select_options',
						config: config,
					},
					response => {
						// Save the new items
						saveItems(saveItemsCache, response.data);

						// Send back the response in case it is needed
						resolve(response.data);
					},
					function (message) {
						reject(message);
					},
				);
			});
		}
	}

	function getItems(config) {
		const saveItemsCache = generateItemsCacheKey(toRaw(config));
		return get(items.value, saveItemsCache, []);
	}

	function getItem(config, id) {
		const saveItemsCache = generateItemsCacheKey(toRaw(config));
		const cachedItems = get(items.value, saveItemsCache, []);
		return cachedItems.find(item => item.id === id);
	}

	function generateItemsCacheKey(config) {
		const { server_callback_method, server_callback_args } = config;

		return hash({
			server_callback_method,
			server_callback_args,
		});
	}

	function generateCacheKey(data) {
		const { server_callback_method, server_callback_args, page, searchKeyword } = data;

		return hash({
			server_callback_method,
			server_callback_args,
			page,
			searchKeyword,
		});
	}

	function saveItems(key, newItems) {
		const existingItems = get(items.value, key, []);
		items.value[key] = unionBy(existingItems, newItems, 'id');
	}

	return {
		fetch,
		getItem,
		getItems,
	};
}
