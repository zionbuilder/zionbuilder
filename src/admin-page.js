import Vue from 'vue'
import VueRouter from 'vue-router'

import ZionBuilderApi from './admin/admin-page/ZionBuilderApi'

// Import global components
import LocalizationPlugin from './common/plugins/l10n'

// Main
import App from './admin/admin-page/App.vue'
import store from './admin/admin-page/store/index'

// Set Service Interceptor
import ZionService from '@/api/ZionService'
import { errorInterceptor } from './api/ServiceInterceptor'
import { Forms } from '@zb/plugins'

// Components
import SideMenu from '@/admin/admin-page/components/SideMenu.vue'
import PageTemplate from './admin/admin-page/components/PageTemplate.vue'
import ListAnimate from './admin/admin-page/components/ListAnimate'
import ModalTwoColTemplate from './admin/admin-page/components/ModalTwoColTemplate'

const ZionBuilderAdmin = {
	init () {
		Vue.use(VueRouter)
		Vue.component('SideMenu', SideMenu)
		Vue.component('PageTemplate', PageTemplate)
		Vue.component('ListAnimation', ListAnimate)
		Vue.component('ModalTwoColTemplate', ModalTwoColTemplate)

		// Plugins
		Vue.use(Forms)
		Vue.use(LocalizationPlugin, ZionBuilderApi.L10n)

		// Add error interceptor for API
		errorInterceptor(ZionService, store)

		// Trigger event so others can hook into ZionBuilder API
		const evt = new CustomEvent('zionbuilder/admin/init', {
			detail: ZionBuilderApi
		})

		window.dispatchEvent(evt)

		const router = new VueRouter({
			routes: ZionBuilderApi.routes.getConfigForRouter()
		})

		/* eslint-disable no-new */
		new Vue({
			el: '#znpb-admin',
			router,
			store,
			render: h => h(App)
		})
	}
}

ZionBuilderAdmin.init()
