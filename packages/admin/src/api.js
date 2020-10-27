import routes from './router'

// Utils
import { errorInterceptor } from '@zionbuilder/rest'
import { addFilter } from '@zb/hooks'
import { useInjections } from '@zb/components'
window.zb = window.zb || {}

const api = {
	routes,
	interceptors: {
		errorInterceptor
	},
	addFilter,
	useInjections
}

// Add to global object
window.zb.admin = api

// Export as module
export default api
