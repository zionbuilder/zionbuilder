
import { createApp } from 'vue'
import { store } from './store/'

// Main
import { forms, Icon, Button } from '@zb/components'
import App from './App.vue'
import { errorInterceptor } from '@zb/rest'
import { install } from '@zb/i18n'
import { createInstance } from './utils/events'
export * as optionsInstance from './manager/options/optionsInstance'

import { initPanels } from './data'

const pageEvents = createInstance()
pageEvents.addDocument(window)

// init data
const panels = initPanels()

const appInstance = createApp(App)

// Plugins
appInstance.use(forms)
appInstance.component('Icon', Icon)
appInstance.component('Button', Button)
appInstance.use({ install }, window.ZnPbInitalData.l10n)
appInstance.use(store)

// Add error interceptor for API
errorInterceptor(store)



// Add editor methods and utilities to all components
appInstance.config.globalProperties.$zb = {
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
