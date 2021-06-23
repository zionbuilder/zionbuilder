<template>
	<component
		v-for="(customComponent, i) in computedComponents"
		:key="i"
		:is="customComponent"
	/>
</template>

<script>
import { computed } from 'vue'
import { useInjections } from '@composables/useInjections'

export default {
	inheritAttrs: false,
	name: 'Injection',
	props: {
		location: {
			type: String,
			required: true
		},
		htmlTag: {
			type: String,
			required: false,
			default: 'div'
		}
	},
	setup (props) {
		const { getComponentsForLocation } = useInjections()
		const computedComponents = computed(() => getComponentsForLocation(props.location))

		return {
			computedComponents
		}
	}
}
</script>
