import Tabs from './components/Tabs.vue';
import TabsItem from './components/TabsItem.vue';

window.zb.editor.registerElementComponent({
	elementType: 'tabs_item',
	component: TabsItem,
});

window.zb.editor.registerElementComponent({
	elementType: 'tabs',
	component: Tabs,
});
