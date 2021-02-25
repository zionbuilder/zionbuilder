import { createApp } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'
import { useNotifications } from '@zionbuilder/composables'

import api from './api'
import { initRoutes } from './router'

// Main
import App from './App.vue'

// Set Service Interceptor
import { errorInterceptor } from '@zionbuilder/rest'

import { install as ComponentsInstall } from '@zb/components'
import { install as I18nInstall } from '@zb/i18n'
import { applyFilters } from '@zb/hooks'
// Components
import SideMenu from './components/SideMenu.vue'
import PageTemplate from './components/PageTemplate.vue'
import ListAnimate from './components/ListAnimate.vue'
import ModalTwoColTemplate from './components/ModalTwoColTemplate.vue'

// Exports
export * from '@zionbuilder/composables'

const appInstance = createApp(App)

appInstance.component('SideMenu', SideMenu)
appInstance.component('PageTemplate', PageTemplate)
appInstance.component('ListAnimation', ListAnimate)
appInstance.component('ModalTwoColTemplate', ModalTwoColTemplate)

// Plugins
appInstance.use(ComponentsInstall)
appInstance.use(I18nInstall, window.ZnI18NStrings)

const notifications = useNotifications()

window.addEventListener('load', function () {
	// Trigger event so others can hook into ZionBuilder API
	const evt = new CustomEvent('zionbuilder/admin/init', {
		detail: api
	})

	// Add error interceptor for API
	errorInterceptor(notifications)

	// Add default routes
	initRoutes()

	window.dispatchEvent(evt)

	const router = applyFilters('zionbuilder/router', createRouter({
		// 4. Provide the history implementation to use. We are using the hash history for simplicity here.
		history: createWebHashHistory(),
		routes: api.routes.getConfigForRouter(), // short for `routes: routes`
	}))



	appInstance.use(router)
	appInstance.mount('#znpb-admin')
})
