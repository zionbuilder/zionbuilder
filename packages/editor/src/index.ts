
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

// Components
import App from './App.vue'

// Data
import { initPanels } from './data'


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

// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
	hooks,
	appInstance,
	pageEvents,
	panels
}

appInstance.mount('#znpb-app')

// Export so we can access them from window.zb.editor
export {
	appInstance,
	pageEvents,
	panels
}
