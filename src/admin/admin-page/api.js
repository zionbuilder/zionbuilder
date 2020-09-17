import routes from './router'
import store from './store'
import { addFilter, applyFilters } from '@/utils/filters'

// Utils
import * as utils from '@/utils'
import { errorInterceptor } from '@/api/ServiceInterceptor'

window.zb = window.zb || {}

const api = {
	routes,
	store,
	utils,
	interceptors: {
		errorInterceptor
	},

	// Filters
	addFilter,
	applyFilters
}

// Add to global object
window.zb.admin = api

// Export as module
export default api
