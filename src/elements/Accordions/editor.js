import accordionItem from './components/accordionItem.vue';
import Accordions from './components/Accordions.vue';

window.zb.editor.registerElementComponent({
	elementType: 'accordions',
	component: Accordions,
});

window.zb.editor.registerElementComponent({
	elementType: 'accordion_item',
	component: accordionItem,
});
