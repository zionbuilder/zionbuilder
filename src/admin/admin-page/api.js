import routes from './router'
import { store } from './store'

// Utils
import { errorInterceptor } from '@/api/ServiceInterceptor'

window.zb = window.zb || {}

const api = {
	routes,
	store,
	interceptors: {
		errorInterceptor
	}
}

// Add to global object
window.zb.admin = api

// Export as module
export default api
