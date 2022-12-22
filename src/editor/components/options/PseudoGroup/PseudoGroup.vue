<template>
	<OptionsForm v-model="valueModel" class="znpb-option--pseudo-group" :schema="child_options" />
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const { usePseudoSelectors } = window.zb.composables;

const props = withDefaults(
	defineProps<{
		modelValue?: Record<string, unknown>;
		// eslint-disable-next-line @typescript-eslint/no-explicit-any, vue/prop-name-casing
		child_options: Record<string, any>;
		// eslint-disable-next-line vue/prop-name-casing
		save_to_id?: boolean;
	}>(),
	{
		modelValue: () => {
			return {};
		},
		save_to_id: false,
	},
);

const emit = defineEmits(['update:modelValue']);

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
</script>

<style lang="scss">
.znpb-options-form-wrapper.znpb-option--pseudo-group {
	padding: 0;
}
</style>
