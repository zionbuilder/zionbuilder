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
import { install as I18nInstall } from '@zionbuilder/i18n'

// Components
import SideMenu from './components/SideMenu.vue'
import PageTemplate from './components/PageTemplate.vue'
import ListAnimate from './components/ListAnimate.vue'
import ModalTwoColTemplate from './components/ModalTwoColTemplate.vue'
import { Users, GoogleFonts, Options, Templates } from '@zionbuilder/models'

// Exports
export * from '@zionbuilder/composables'

const appInstance = createApp(App)

appInstance.component('SideMenu', SideMenu)
appInstance.component('PageTemplate', PageTemplate)
appInstance.component('ListAnimation', ListAnimate)
appInstance.component('ModalTwoColTemplate', ModalTwoColTemplate)

// Plugins
appInstance.use(ComponentsInstall)
appInstance.use(I18nInstall, window.ZnPbAdminPageData.l10n)

const notifications = useNotifications()
const users = new Users()
const options = new Options()
const templates = new Templates()

// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
	users,
	options,
	templates
}

window.addEventListener('load', function() {
	// Trigger event so others can hook into ZionBuilder API
	const evt = new CustomEvent('zionbuilder/admin/init', {
		detail: api
	})

	// Add error interceptor for API
	errorInterceptor(notifications)

	// Add default routes
	initRoutes()

	window.dispatchEvent(evt)

	const router = createRouter({
		// 4. Provide the history implementation to use. We are using the hash history for simplicity here.
		history: createWebHashHistory(),
		routes: api.routes.getConfigForRouter(), // short for `routes: routes`
	})

	appInstance.use(router)
	appInstance.provide('$zb', appInstance.config.globalProperties.$zb)
	appInstance.mount('#znpb-admin')
})

export {
	users,
	options,
	templates
}