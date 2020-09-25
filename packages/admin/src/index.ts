import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'

import api from './api'
import { initRoutes } from './router'

// Main
import App from './App.vue'
import {store} from './store/'

// Set Service Interceptor
import { ZionService } from '@zb/rest'
import { errorInterceptor } from '@zb/rest'
import { Forms } from '@zb/plugins'
import Localization from '@zb/l10n'

// Components
import SideMenu from './components/SideMenu.vue'
import PageTemplate from './components/PageTemplate.vue'
import ListAnimate from './components/ListAnimate.vue'
import ModalTwoColTemplate from './components/ModalTwoColTemplate.vue'

const ZionBuilderAdmin = {
	init () {
		const appInstance = createApp(App)

		appInstance.component('SideMenu', SideMenu)
		appInstance.component('PageTemplate', PageTemplate)
		appInstance.component('ListAnimation', ListAnimate)
		appInstance.component('ModalTwoColTemplate', ModalTwoColTemplate)

		// Plugins
		// appInstance.use(Forms)
		appInstance.use(Localization, window.ZnPbAdminPageData.l10n)

		// Add error interceptor for API
		errorInterceptor(ZionService, store)

		// Add default routes
		initRoutes()

		const router = createRouter({
			// 4. Provide the history implementation to use. We are using the hash history for simplicity here.
			history: createWebHashHistory(),
			routes: api.routes.getConfigForRouter(), // short for `routes: routes`
		})

		appInstance.use(router)
		appInstance.use(store)

		// Trigger event so others can hook into ZionBuilder API
		const evt = new CustomEvent('zionbuilder/admin/init', {
			detail: api
		})

		window.dispatchEvent(evt)
		appInstance.mount('#znpb-admin')
	}
}

ZionBuilderAdmin.init()
