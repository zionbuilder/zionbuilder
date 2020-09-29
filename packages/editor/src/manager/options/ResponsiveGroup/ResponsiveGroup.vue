<template>
	<OptionsForm
		class="znpb-option--responsive-group"
		:schema="child_options"
		v-model="valueModel"
	/>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
	name: 'ResponsiveGroup',
	showWrappers: false,
	props: {
		value: {},
		child_options: {
			type: Object,
			required: true
		}
	},
	data () {
		return {
			showWrappers: false
		}
	},
	computed: {
		...mapGetters([
			'getActiveDevice'
		]),
		valueModel: {
			get () {
				return (this.value || {})[this.getActiveDevice.id] || {}
			},
			set (newValue) {
				const clonedValue = { ...this.value }
				// Check if we actually need to delete the option
				if (newValue === null && typeof clonedValue[this.getActiveDevice.id]) {
					// If this is used as layout, we need to delete the active pseudo selector
					delete clonedValue[this.getActiveDevice.id]
				} else {
					clonedValue[this.getActiveDevice.id] = newValue
				}

				// Send the updated value back
				this.$emit('input', clonedValue)
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
		updateValue (newValue) {

		},
		removeDeviceStyles (device) {
			const clonedValues = { ...this.value }
			delete clonedValues[device]

			this.$emit('input', clonedValues)
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
