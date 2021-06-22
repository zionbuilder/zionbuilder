import routes from './router'

// Utils
import {
	errorInterceptor
} from '@zionbuilder/rest'
import {
	addFilter
} from '@zb/hooks'
import {
	useInjections
} from '@zb/components'
import {
	ServerRequest
} from '@zb/utils'

const serverRequest = new ServerRequest()

window.zb = window.zb || {}

const api = {
	routes,
	interceptors: {
		errorInterceptor
	},
	addFilter,
	useInjections,
	serverRequest
}

// Add to global object
window.zb.admin = api

// Export as module
export default api
