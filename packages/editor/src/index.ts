require('./scss/index.scss')
import { createApp } from 'vue'
import { store } from './store/'
export * from './data'
// Main
import * as hooks from '@zb/hooks'
import { useElementTypes } from './data'
import { registerEditorOptions } from './components/options'
// Plugins
import { install as ComponentsInstall } from '@zb/components'
import { install as L18NInstall } from '@zb/i18n'
import { errorInterceptor } from '@zb/rest'
import { createInstance } from './utils/events'
import { Errors, Options, Templates } from '@zionbuilder/models'
import {
	TreeViewList,
	TreeViewListItem
} from './components/treeView'
import {
	WireframeList,
	WireframeListItem
} from './components/treeView'
import { useOptionsSchemas } from '@zb/components'

// Register editor options schemas
const { registerSchema } = useOptionsSchemas()
registerSchema('pageSettingsSchema', window.ZnPbInitalData.page_settings.schema)

// Register options
registerEditorOptions()

// Components
import App from './App.vue'

// init data
const errors = new Errors()
const options = new Options()
const templates = new Templates()
const appInstance = createApp(App)

// Init global components
appInstance.use(L18NInstall, window.ZnPbInitalData.l10n)
appInstance.use(ComponentsInstall)
appInstance.use(store)

// Add error interceptor for API
errorInterceptor(errors)

// Register nested components
appInstance.component('TreeViewList', TreeViewList)
appInstance.component('TreeViewListItem', TreeViewListItem)
appInstance.component('WireframeList', WireframeList)
appInstance.component('WireframeListItem', WireframeListItem)

// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
	errors,
	options,
	templates,
	hooks,
	appInstance,
	urls: window.ZnPbInitalData.urls
}
appInstance.provide('$zb', appInstance.config.globalProperties.$zb)
appInstance.mount('#znpb-app')

// Expose common methods
const { registerElementComponent } = useElementTypes()

// Export so we can access them from window.zb.editor
export {
	appInstance,
	errors,
	options,
	templates,
	registerElementComponent
}
