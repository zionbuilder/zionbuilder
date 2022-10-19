<template>
	<OptionsForm v-model="valueModel" class="znpb-option--pseudo-group" :schema="child_options" />
</template>

<script>
import { computed } from 'vue';
import { usePseudoSelectors } from '/@/common/composables';

export default {
	name: 'PseudoGroup',
	props: {
		modelValue: {},
		child_options: {
			type: Object,
			required: true,
		},
		save_to_id: {
			type: Boolean,
		},
	},
	setup(props, { emit }) {
		const { activePseudoSelector } = usePseudoSelectors();
		const valueModel = computed({
			get() {
				return (props.modelValue || {})[activePseudoSelector.value.id] || {};
			},
			set(newValue) {
				const clonedValue = { ...props.modelValue };
				// Check if we actually need to delete the option
				if (newValue === null && typeof clonedValue[activePseudoSelector.value.id]) {
					// If this is used as layout, we need to delete the active pseudo selector
					delete clonedValue[activePseudoSelector.value.id];
				} else {
					clonedValue[activePseudoSelector.value.id] = newValue;
				}

				// Send the updated value back
				emit('update:modelValue', clonedValue);
			},
		});

		return {
			activePseudoSelector,
			valueModel,
		};
	},
};
</script>

<style lang="scss">
.znpb-options-form-wrapper.znpb-option--pseudo-group {
	padding: 0;
}
</style>
