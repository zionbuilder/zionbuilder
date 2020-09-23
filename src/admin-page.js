import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'

import api from './admin/admin-page/api'
import { initRoutes } from '@/admin/admin-page/router'

// Main
import App from './admin/admin-page/App.vue'
import {store} from './admin/admin-page/store/'

// Set Service Interceptor
import ZionService from '@/api/ZionService'
import { errorInterceptor } from './api/ServiceInterceptor'
import { Forms } from '@zb/plugins'
import Localization from '@zb/l10n'

// Components
import SideMenu from '@/admin/admin-page/components/SideMenu.vue'
import PageTemplate from './admin/admin-page/components/PageTemplate.vue'
import ListAnimate from './admin/admin-page/components/ListAnimate'
import ModalTwoColTemplate from './admin/admin-page/components/ModalTwoColTemplate'

const ZionBuilderAdmin = {
	init () {
		const appInstance = createApp(App)

		appInstance.component('SideMenu', SideMenu)
		appInstance.component('PageTemplate', PageTemplate)
		appInstance.component('ListAnimation', ListAnimate)
		appInstance.component('ModalTwoColTemplate', ModalTwoColTemplate)

		// Plugins
		appInstance.use(Forms)
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
