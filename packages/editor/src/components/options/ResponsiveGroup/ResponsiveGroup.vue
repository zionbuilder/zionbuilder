<template>
	<OptionsForm
		class="znpb-option--responsive-group"
		:schema="child_options"
		v-model="valueModel"
	/>
</template>

<script>
import { useResponsiveDevices } from '@zb/components'

export default {
	name: 'ResponsiveGroup',
	showWrappers: false,
	props: {
		modelValue: {},
		child_options: {
			type: Object,
			required: true
		}
	},
	setup () {
		const { activeResponsiveDeviceInfo } = useResponsiveDevices()

		return {
			activeResponsiveDeviceInfo
		}
	},

	data () {
		return {
			showWrappers: false
		}
	},
	computed: {
		valueModel: {
			get () {
				return (this.modelValue || {})[this.activeResponsiveDeviceInfo.id] || {}
			},
			set (newValue) {
				const clonedValue = { ...this.modelValue }
				// Check if we actually need to delete the option
				if (newValue === null && typeof clonedValue[this.activeResponsiveDeviceInfo.id]) {
					// If this is used as layout, we need to delete the active pseudo selector
					delete clonedValue[this.activeResponsiveDeviceInfo.id]
				} else {
					clonedValue[this.activeResponsiveDeviceInfo.id] = newValue
				}

				// Send the updated value back
				this.$emit('update:modelValue', clonedValue)
			}
		}
	},
	mounted () {
		// Set the active responsive group
		window.zb.editor.options.setActiveResponsiveOptions(this)
	},
	beforeUnmount () {
		// Set the active responsive group
		window.zb.editor.options.removeActiveResponsiveOptions()
	},
	methods: {
		removeDeviceStyles (device) {
			const clonedValues = { ...this.modelValue }
			delete clonedValues[device]

			this.$emit('update:modelValue', clonedValues)
		}
	}

}
</script>
<style lang="scss">
.znpb-options-form-wrapper.znpb-option--responsive-group {
	flex: 1;
	padding: 0;
}
</style>
