<template>
	<OptionsForm
		class="znpb-option--pseudo-group"
		:schema="child_options"
		v-model="valueModel"
	/>
</template>

<script>
import { useResponsiveDevices, usePseudoSelectors } from '@zb/components'

export default {
	name: 'PseudoGroup',
	props: {
		modelValue: {},
		child_options: {
			type: Object,
			required: true
		},
		save_to_id: {
			type: Boolean
		}
	},
	setup() {
		const { activePseudoSelector } = usePseudoSelectors()

		return {
			activePseudoSelector
		}
	},
	computed: {
		optionId () {
			return this.$parent.optionId
		},
		valueModel: {
			get () {
				return (this.modelValue || {})[this.activePseudoSelector.value.id] || {}
			},
			set (newValue) {
				const clonedValue = { ...this.modelValue }
				// Check if we actually need to delete the option
				if (newValue === null && typeof clonedValue[this.activePseudoSelector.value.id]) {
					// If this is used as layout, we need to delete the active pseudo selector
					delete clonedValue[this.activePseudoSelector.value.id]
				} else {
					clonedValue[this.activePseudoSelector.value.id] = newValue
				}

				// Send the updated value back
				this.$emit('update:modelValue', clonedValue)
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
