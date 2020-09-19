
import PreviewApp from '@/preview/App.vue'
import Vue from '@zb/vue'

// Preview related
import RenderValue from '@/preview/components/RenderValue.vue'
import ElementIcon from '@/preview/components/ElementIcon.vue'
import SortableContent from '@/preview/components/SortableContent.vue'
import Element from '@/preview/components/Element.vue'
import RenderTag from '@/preview/components/RenderTag'
import RenderTagGroup from '@/preview/components/RenderTagGroup'
import ScriptsLoader from '@/preview/utils/ScriptsLoader'

// Import WordPress related
import './wordpress'

// Load components only for styles as they are already registered on master Vue instance
require('@/editor/components/elementOptions/forms/OptionsForm.vue')
require('@/editor/manager/options')

// Load general components needed for preview
Vue.component('RenderValue', RenderValue)
Vue.component('RenderTag', RenderTag)
Vue.component('RenderTagGroup', RenderTagGroup)
Vue.component('ElementIcon', ElementIcon)
Vue.component('SortableContent', SortableContent)
Vue.component('Element', Element)

const debug = process.env.NODE_ENV !== 'production'
Vue.config.devtools = debug
Vue.config.performance = debug

window.zb = window.zb || {}

window.zb.preview = {
	scripts: new ScriptsLoader()
}

window.zb.editor = window.parent.zb.editor

const ZionBuilder = {
	init () {
		// Trigger event so others can hook into ZionBuilder API
		const beforeInitEvent = new CustomEvent('zionbuilder/preview/beforeInit', {
			detail: window.zb
		})

		window.dispatchEvent(beforeInitEvent)

		/* eslint-disable no-new */
		const vueInstanceConfig = {
			el: document.getElementById('znpb-preview-content-area'),
			store: window.zb.editor.store,
			render: h => h(PreviewApp),
			name: 'Preview'
		}

		// Only set the parent in dev mode so we can use dev tools
		if (debug) {
			vueInstanceConfig.parent = window.parent.zb.editor.editorComponent
		}

		const previewInstance = new Vue(vueInstanceConfig)

		// Trigger event so others can hook into ZionBuilder API
		const afterInitEvent = new CustomEvent('zionbuilder/preview/afterInit', {
			detail: window.zb
		})

		window.dispatchEvent(afterInitEvent)

		// Destroy the instance before unload
		// Fixes inline editor error bug when changing page template
		window.addEventListener('beforeunload', (event) => {
			previewInstance.$destroy()
		})
	}
}

ZionBuilder.init()
