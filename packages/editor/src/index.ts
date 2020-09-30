
import { createApp } from 'vue'
import { store } from './store/'

// Main
import { forms, Icon, Button } from '@zb/components'
import App from './App.vue'
import { errorInterceptor } from '@zb/rest'
import { install } from '@zb/i18n'

export * as optionsInstance from './manager/options/optionsInstance'

const appInstance = createApp(App)

// Plugins
appInstance.use(forms)
appInstance.component('Icon', Icon)
appInstance.component('Button', Button)
appInstance.use({ install }, window.ZnPbInitalData.l10n)
appInstance.use(store)

// Add error interceptor for API
errorInterceptor(store)

appInstance.mount('#znpb-app')

