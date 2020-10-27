import routes from './router'

// Utils
import { errorInterceptor } from '@zionbuilder/rest'
import { addFilter } from '@zb/hooks'
window.zb = window.zb || {}

const api = {
	routes,
	interceptors: {
		errorInterceptor
	},
	addFilter
}

// Add to global object
window.zb.admin = api

// Export as module
export default api
