
import { createApp } from 'vue'
import { store } from './store/'

// Main
import * as hooks from '@zb/hooks'

// Plugins
import { install as ComponentsInstall } from '@zb/components'
import { install as L18NInstall } from '@zb/i18n'
import { errorInterceptor } from '@zb/rest'
import { createInstance } from './utils/events'
import { initElements, initElementCategories } from './data/elements'
import { Errors } from '@zionbuilder/models'
import * as interactions from './interactions/'
import {
	TreeViewList,
	TreeViewListItem
} from './components/treeView'

// Components
import App from './App.vue'

// Data
import { initPanels } from './data'

// It will allow us to add events to both the editor document and iframe document
const pageEvents = createInstance()
pageEvents.addDocument(window)

// init data
const panels = initPanels()
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

// Init elements registration
const elements = initElements()
const elementCategories = initElementCategories()

// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
	data: {
		elements,
		elementCategories
	},
	editor: {
		interactions
	},
	errors,
	hooks,
	appInstance,
	pageEvents,
	panels,
	urls: window.ZnPbInitalData.urls
}

appInstance.mount('#znpb-app')

// Export so we can access them from window.zb.editor
export {
	appInstance,
	pageEvents,
	panels,
	elements,
	elementCategories,
	errors,
	interactions
}
