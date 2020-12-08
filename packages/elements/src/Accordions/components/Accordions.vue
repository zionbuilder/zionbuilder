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
import { provide } from 'vue'
import accordionItem from './accordionItem.vue'
export default {
	name: 'accordions',
	components: {
		accordionItem
	},
	props: ['options', 'element', 'api'],
	setup (props) {
		// Check to see if we need to add some accordions
		if (props.element.content.length === 0 && props.options.items) {
			props.element.addChildren(props.options.items)
		}

		provide('accordionsApi', props.api)
	}
}

</script>
