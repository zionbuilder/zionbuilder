import Vue from 'vue'
import VueRouter from 'vue-router'

import ZionBuilderApi from './admin/admin-page/ZionBuilderApi'

// Import global components
import LocalizationPlugin from './common/plugins/l10n'

import Tabs from '@/common/components/tabs/tabs.vue'
import Tab from '@/common/components/tabs/tab.vue'
import ClickOutside from './common/plugins/clickOutside'

// Main
import App from './admin/admin-page/App.vue'
import store from './admin/admin-page/store/index'

// Set Service Interceptor
import ZionService from '@/api/ZionService'
import { errorInterceptor } from './api/ServiceInterceptor'
import { Forms } from '@/common/components/forms'

// Components
import BaseIcon from '@/common/components/BaseIcon.vue'
import BaseButton from '@/common/components/BaseButton.vue'
import Modal from '@/common/components/modal/Modal.vue'
import ModalConfirm from '@/common/components/modal/ModalConfirm.vue'
import Tooltip from '@/common/components/tooltip'
import SideMenu from '@/admin/admin-page/components/SideMenu.vue'
import Notice from '@/common/components/Notice'
import PageTemplate from './admin/admin-page/components/PageTemplate.vue'
import ListAnimate from './admin/admin-page/components/ListAnimate'
import ModalTwoColTemplate from './admin/admin-page/components/ModalTwoColTemplate'
import Loader from './common/components/Loader'
import OptionsForm from '@/editor/components/elementOptions/forms/OptionsForm.vue'

const ZionBuilderAdmin = {
	init () {
		Vue.use(VueRouter)
		Vue.component('BaseIcon', BaseIcon)
		Vue.component('BaseButton', BaseButton)
		Vue.component('Modal', Modal)
		Vue.component('ModalConfirm', ModalConfirm)
		Vue.component('SideMenu', SideMenu)
		Vue.component('Notice', Notice)
		Vue.component('PageTemplate', PageTemplate)
		Vue.component('ListAnimation', ListAnimate)
		Vue.component('ModalTwoColTemplate', ModalTwoColTemplate)
		Vue.component('Loader', Loader)
		Vue.component('OptionsForm', OptionsForm)
		// Tabs
		Vue.component('Tabs', Tabs)
		Vue.component('Tab', Tab)

		Vue.use(ClickOutside)
		Vue.use(Forms)
		Vue.use(Tooltip, {
			trigger: 'hover',
			placement: 'top',
			appendTo: 'body',
			modifiers: {
				offset: {
					offset: '0,10px'
				}
			}
		})

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
