<template>
	<div>
		<slot name="start" />
		<accordionItem
			v-for="(item, i) in options.items"
			:key="i"
			:element="item"
			:options="item.options"
		/>
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
