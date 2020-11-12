<template>
	<component
		:is="htmlTag"
		class="znpb-injection-component"
	>
		<component
			v-for="(customComponent, i) in getComponents"
			:key="i"
			:is="customComponent"
		/>
	</component>
</template>

<script>
import { useInjections } from '@composables'
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
	setup () {
		const { getComponentsForLocation } = useInjections()
		const getComponents = getComponentsForLocation

		return {
			getComponents
		}
	}
}
</script>

<style lang="scss">
.znpb-injection-component {
	display: flex;
}
</style>
