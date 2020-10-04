
import { createVNode, render } from 'vue'

declare global {
	interface Window {
		ZnPbInitalData: any
	}
}

// Main
// import { forms, Icon, Button } from '@zionbuilder/components'
import App from './App.vue'
// import { install } from '@zionbuilder/i18n'

const vNode = createVNode(App, {})
vNode.appContext = window.parent.zb.editor.appInstance._context
// const appInstance = createApp(App)
const renderEl = document.getElementById('znpb-preview-content-area')
render(vNode, renderEl)
// Plugins
// appInstance.use(forms)
// appInstance.component('Icon', Icon)
// appInstance.component('Button', Button)
// appInstance.use({ install }, window.parent.ZnPbInitalData.l10n)


// appInstance.mount('#znpb-preview-content-area')

