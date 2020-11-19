<template>
	<component
		:is="htmlTag"
		class="znpb-injection-component"
	>
		<component
			v-for="(customComponent, i) in computedComponents"
			:key="i"
			:is="customComponent"
		/>
	</component>
</template>

<script>
import { computed } from 'vue'
import { useInjections } from '@composables/useInjections'

export default {
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

<style lang="scss">
.znpb-injection-component {
	display: flex;
}
</style>
