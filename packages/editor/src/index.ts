require('./scss/index.scss')
import { createApp } from 'vue'
import { store } from './store/'
export * from './data'
// Main
import * as hooks from '@zb/hooks'
import { useElementTypes } from './data'

// Plugins
import { install as ComponentsInstall } from '@zb/components'
import { install as L18NInstall } from '@zb/i18n'
import { errorInterceptor } from '@zb/rest'
import { createInstance } from './utils/events'
import { Errors } from '@zionbuilder/models'
import {
	TreeViewList,
	TreeViewListItem
} from './components/treeView'
import {
	WireframeList,
	WireframeListItem
} from './components/treeView'


// Components
import App from './App.vue'

// It will allow us to add events to both the editor document and iframe document
const pageEvents = createInstance()
pageEvents.addDocument(window)

// init data
const errors = new Errors()
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
	hooks,
	appInstance,
	pageEvents,
	urls: window.ZnPbInitalData.urls
}

appInstance.mount('#znpb-app')

// Expose common methods
const { registerElementComponent } = useElementTypes()

// Export so we can access them from window.zb.editor
export {
	appInstance,
	pageEvents,
	errors,
	registerElementComponent
}
