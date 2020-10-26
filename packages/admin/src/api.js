import routes from './router'

// Utils
import { errorInterceptor } from '@zionbuilder/rest'

window.zb = window.zb || {}

const api = {
	routes,
	interceptors: {
		errorInterceptor
	}
}

// Add to global object
window.zb.admin = api

// Export as module
export default api
