<template>
	<OptionsForm v-model="valueModel" class="znpb-option--responsive-group" :schema="child_options" />
</template>

<script>
import { onMounted, onBeforeUnmount, computed } from 'vue';
import { useResponsiveDevices } from '/@/common/composables';

export default {
	name: 'ResponsiveGroup',
	showWrappers: false,
	props: {
		modelValue: {},
		child_options: {
			type: Object,
			required: true,
		},
	},
	setup(props, { emit }) {
		const { activeResponsiveDeviceInfo, setActiveResponsiveOptions, removeActiveResponsiveOptions } =
			useResponsiveDevices();
		const modelValue = computed(() => props.modelValue);

		onMounted(() =>
			setActiveResponsiveOptions({
				modelValue,
				removeDeviceStyles,
			}),
		);
		onBeforeUnmount(() => removeActiveResponsiveOptions());

		function removeDeviceStyles(device) {
			const clonedValues = { ...this.modelValue };
			delete clonedValues[device];

			emit('update:modelValue', clonedValues);
		}

		return {
			activeResponsiveDeviceInfo,
		};
	},

	data() {
		return {
			showWrappers: false,
		};
	},
	computed: {
		valueModel: {
			get() {
				return (this.modelValue || {})[this.activeResponsiveDeviceInfo.id] || {};
			},
			set(newValue) {
				const clonedValue = { ...this.modelValue };
				// Check if we actually need to delete the option
				if (newValue === null && typeof clonedValue[this.activeResponsiveDeviceInfo.id]) {
					// If this is used as layout, we need to delete the active pseudo selector
					delete clonedValue[this.activeResponsiveDeviceInfo.id];
				} else {
					clonedValue[this.activeResponsiveDeviceInfo.id] = newValue;
				}

				// Send the updated value back
				this.$emit('update:modelValue', clonedValue);
			},
		},
	},
};
</script>
<style lang="scss">
.znpb-options-form-wrapper.znpb-option--responsive-group {
	flex: 1;
	padding: 0;
}
</style>
