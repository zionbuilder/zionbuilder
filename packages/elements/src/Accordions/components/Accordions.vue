<template>
	<div>
		<slot name="start" />
		<accordionItem
			v-for="(item, i) in options.items"
			:key="i"
			:element="item"
			:options="item.options"
		>
			<Element
				v-for="(element, i) in element.content"
				:key="element.uid"
				:element="element"
			/>
		</accordionItem>
		<slot name="end" />
	</div>
</template>

<script>
import accordionItem from './accordionItem.vue'
export default {
	name: 'accordions',
	components: {
		accordionItem
	},
	props: ['options', 'element', 'api'],
}

</script>
