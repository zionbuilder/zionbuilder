<template>
	<div class="znpb-option-cssChildSelectorPseudoSelector">
		<template v-if="states.length === 1">
			{{states[0]}}
		</template>
	</div>
</template>

<script>
import { computed } from 'vue'

export default {
	name: 'PseudoSelector',
	props: {
		states: {
			type: Array,
			default: []
		}
	},
	setup (props) {
		const computedStates = computed({
			get () {
				return props.states || []
			},
			set (newStates) {
				emit('update:states', newStates)
			}
		})

		return {
			computedStates
		}
	}
}
</script>

<style>
</style>