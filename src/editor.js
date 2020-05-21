import './wordpress'
import './editor/AxiosInterceptors'

import Accordion from '@/common/components/accordion/Accordion.vue'
import BaseButton from './common/components/BaseButton.vue'
import BaseIcon from './common/components/BaseIcon.vue'
import BasePanel from './editor/components/BasePanel.vue'
import EditorApp from './editor/App.vue'
import ElementWireframeView from '@/editor/components/treeView/wireFrame/ElementWireframeView'
import FlyoutWrapper from './editor/components/FlyoutWrapper.vue'
import HorizontalAccordion from '@/common/components/horizontal-accordion/HorizontalAccordion.vue'
import L10n from './editor/L10n'
import Loader from './common/components/Loader'
import Localization from './common/plugins/l10n'
import Notice from '@/common/components/Notice'
import OptionsForm from '@/editor/components/elementOptions/forms/OptionsForm.vue'
import Tab from '@/common/components/tabs/tab.vue'
import Tabs from '@/common/components/tabs/tabs.vue'
import Vue from 'vue'
import ZionApi from './editorApi'
import ZionService from '@/api/ZionService'
import { errorInterceptor } from './api/ServiceInterceptor'
import store from './editor/store/index'

const debug = process.env.NODE_ENV !== 'production'
Vue.config.devtools = debug
Vue.config.performance = debug

// Translation
Vue.use(Localization, L10n)

const ZionBuilder = {
	init () {
		// Load global Vue Components
		Vue.component('BaseIcon', BaseIcon)
		Vue.component('FlyoutWrapper', FlyoutWrapper)
		Vue.component('BasePanel', BasePanel)
		Vue.component('BaseButton', BaseButton)
		Vue.component('Tabs', Tabs)
		Vue.component('Tab', Tab)
		Vue.component('Accordion', Accordion)
		Vue.component('HorizontalAccordion', HorizontalAccordion)
		Vue.component('Notice', Notice)
		Vue.component('OptionsForm', OptionsForm)
		Vue.component('ElementWireframeView', ElementWireframeView)
		Vue.component('Loader', Loader)

		// Add error interceptor for API
		errorInterceptor(ZionService, store)

		/* eslint-disable no-new */
		ZionApi.editorComponent = new Vue({
			el: '#znpb-app',
			store,
			render: h => h(EditorApp)
		})

		ZionApi.store = store

		// Trigger event so others can hook into ZionBuilder API
		const evt = new CustomEvent('zionbuilder/editor/ready', {
			detail: ZionApi
		})

		window.dispatchEvent(evt)
	}
}

ZionBuilder.init()
