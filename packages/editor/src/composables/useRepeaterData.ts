import { provide, inject, watch } from 'vue'

const queryCache = {}

export function useRepeaterData() {
	const repeaterProviderConfig = {}
	const repeaterConsumerConfig = {}

	function queryProviderData(config) {
		const serverRequest = window.zb.editor.serverRequest

		return new Promise((resolve, reject) => {
			serverRequest.request({
				type: 'perform_repeater_query',
				config,
			}, (response) => {
				resolve(response)
			}, function (message) {
				reject(message)
			})
		})
	}

	function getConsumerItems() {

	}

	return {
		queryProviderData
	}
}