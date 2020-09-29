
import { createApp } from 'vue'


// Main
import { forms, BaseIcon, BaseButton } from '@zb/components'
import App from './App.vue'
import { errorInterceptor } from '@zb/rest'
import { install } from '@zb/i18n'

const appInstance = createApp(App)

// Plugins
appInstance.use(forms)
appInstance.component('BaseIcon', BaseIcon)
appInstance.component('BaseButton', BaseButton)
appInstance.use({ install }, window.ZnPbInitalData.l10n)


appInstance.mount('#znpb-app')

