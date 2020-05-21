import ZionService from '@/api/ZionService'
import WPService from '@/api/WPService'
import store from '@/editor/store'

const interceptor = function (config) {
	config.headers['X-WP-Nonce'] = store.getters.getNonce

	return config
}

// Attach interceptors
ZionService.interceptors.request.use(interceptor)
WPService.interceptors.request.use(interceptor)
