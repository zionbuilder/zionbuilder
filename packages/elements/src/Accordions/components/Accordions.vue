<template>
	<div>
		<slot name="start" />

		<Element
			v-for="element in element.content"
			:key="element.uid"
			:element="element"
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
