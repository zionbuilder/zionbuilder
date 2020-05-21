<template>
	<OptionsForm
		class="znpb-option--pseudo-group"
		:schema="child_options"
		v-model="valueModel"
	/>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
	name: 'PseudoGroup',
	props: {
		value: {},
		child_options: {
			type: Object,
			required: true
		},
		save_to_id: {
			type: Boolean
		}
	},
	computed: {
		...mapGetters([
			'getActivePseudoSelector'
		]),
		optionId () {
			return this.$parent.optionId
		},
		valueModel: {
			get () {
				return (this.value || {})[this.getActivePseudoSelector.id] || {}
			},
			set (newValue) {
				const clonedValue = { ...this.value }
				// Check if we actually need to delete the option
				if (newValue === null && typeof clonedValue[this.getActivePseudoSelector.id]) {
					// If this is used as layout, we need to delete the active pseudo selector
					delete clonedValue[this.getActivePseudoSelector.id]
				} else {
					clonedValue[this.getActivePseudoSelector.id] = newValue
				}

				// Send the updated value back
				this.$emit('input', clonedValue)
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-options-form-wrapper.znpb-option--pseudo-group {
	padding: 0;
}
</style>
