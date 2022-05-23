import { registerElementComponent } from '@zb/editor'
import Tabs from './components/Tabs.vue'
import TabsItem from './components/TabsItem.vue'

registerElementComponent({
	elementType: 'tabs_item',
	component: TabsItem
})

registerElementComponent({
	elementType: 'tabs',
	component: Tabs
})
