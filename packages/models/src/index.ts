
import { createApp } from 'vue'
import { store } from './store/'

// Main
import * as hooks from '@zb/hooks'

// Plugins
import { install as ComponentsInstall } from '@zb/components'
import { install as L18NInstall } from '@zb/i18n'
import { errorInterceptor } from '@zb/rest'
import { createInstance } from './utils/events'
export * as optionsInstance from './manager/options/optionsInstance'
import { initElements, initElementCategories } from './data/elements'
import { PageAreas } from '@zb/models/PageData'

// Components
import App from './App.vue'

// Data
import { initPanels } from './data'

// It will allow us to add events to both the editor document and iframe document
const pageEvents = createInstance()
pageEvents.addDocument(window)

// init data
const panels = initPanels()

const appInstance = createApp(App)

// Init global components
appInstance.use(L18NInstall, window.ZnPbInitalData.l10n)
appInstance.use(ComponentsInstall)
appInstance.use(store)

// Add error interceptor for API
errorInterceptor(store)

// Init elements registration
const elements = initElements()
const elementCategories = initElementCategories()
const pageAreas = new PageAreas()

// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
	data: {
		elements,
		elementCategories,
		pageAreas
	},
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
	pageAreas
}
