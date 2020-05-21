export const errorInterceptor = function (Service, store) {
	// Handle response errors
	Service.interceptors.response.use(function (response) {
		// Do something with response data
		if (typeof response.data !== 'object') {
			// Do something with response error
			store.dispatch('addNotice', {
				title: 'Server error',
				message: 'There was a server error. Please refresh the page and try again',
				type: 'error'
			})
			// eslint-disable-next-line
			console.warn(response)
		}

		return response
	}, function (error) {
		let message = 'There was a problem performing the action. Please try again or refresh the page.'

		if (typeof error.response.data.message !== 'undefined') {
			message = error.response.data.message
		}

		// Do something with response error
		store.dispatch('addNotice', {
			title: 'Server error',
			message: message,
			type: 'error'
		})

		return Promise.reject(error)
	})
}
