
import { createApp } from 'vue'

declare global {
	interface Window {
		ZnPbInitalData: any
	}
}

// Main
import { forms, Icon, Button } from '@zionbuilder/components'
import App from './App.vue'
import { install } from '@zionbuilder/i18n'

const appInstance = createApp(App)

// Plugins
appInstance.use(forms)
appInstance.component('Icon', Icon)
appInstance.component('Button', Button)
appInstance.use({ install }, window.ZnPbInitalData.l10n)


appInstance.mount('#znpb-preview-content-area')

