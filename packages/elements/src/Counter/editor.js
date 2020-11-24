import { registerElementComponent } from '@zb/editor'
import Counter from './components/counter.vue'

registerElementComponent({
	elementType: 'counter',
	component: Counter
})