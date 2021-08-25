import ZionService from './ZionService'

export const errorInterceptor = function (errors, service = ZionService) {
	// Handle response errors
	service.interceptors.response.use(function (response) {
		// Do something with response data
		if (typeof response.data !== 'object') {
			// Do something with response error
			errors.add({
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
		errors.add({
			title: 'Error',
			message: message,
			type: 'error'
		})

		return Promise.reject(error)
	})
}
