import { registerElementComponent } from '@zb/editor'
import accordionItem from './components/accordionItem.vue'
import Accordions from './components/Accordions.vue'

registerElementComponent({
	elementType: 'accordions',
	component: Accordions
})

registerElementComponent({
	elementType: 'accordion_item',
	component: accordionItem
})