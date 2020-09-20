import '@/wordpress'
import '@/editor/AxiosInterceptors'

import {
	Accordion,
	HorizontalAccordion,
	Notice,
	Tabs,
	Tab,
	BaseIcon,
	BaseButton,
	Loader
} from '@zb/components'

import BasePanel from '@/editor/components/BasePanel.vue'
import EditorApp from '@/editor/App.vue'
import ElementWireframeView from '@/editor/components/treeView/wireFrame/ElementWireframeView'
import FlyoutWrapper from '@/editor/components/FlyoutWrapper.vue'
import OptionsForm from '@/editor/components/elementOptions/forms/OptionsForm.vue'
import Vue from '@zb/vue'
import ZionService from '@/api/ZionService'
import { errorInterceptor } from '@/api/ServiceInterceptor'
import store from '@/editor/store/index'
import Localization from '@zb/l10n'
import { addSchemas } from '@zb/schemas'

const debug = process.env.NODE_ENV !== 'production'
Vue.config.devtools = debug
Vue.config.performance = debug

// Translation
Vue.use(Localization, window.ZnPbInitalData.l10n)

const ZionBuilder = {
	init () {
		// Register specific schemas for editor
		addSchemas({ page_settings: window.ZnPbInitalData.page_settings.schema })

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

		const editorComponent = new Vue({
			el: '#znpb-app',
			store,
			render: h => h(EditorApp)
		})

		// Only set the parent in dev mode so we can use dev tools
		if (debug) {
			window.zb.editor.editorComponent = editorComponent
		}

		window.zb.editor.store = store

		// Trigger event so others can hook into ZionBuilder API
		const evt = new CustomEvent('zionbuilder/editor/ready', {
			detail: window.zb
		})

		window.dispatchEvent(evt)
	}
}

ZionBuilder.init()
