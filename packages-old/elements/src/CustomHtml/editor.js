import { registerElementComponent } from '@zb/editor'
import customHtml from './components/customHtml.vue'

registerElementComponent({
	elementType: 'custom_html',
	component: customHtml
})
