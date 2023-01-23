<template>
	<OptionsForm v-model="valueModel" :schema="computedSchema" class="znpb-option__typography-wrapper" />
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue?: Record<string, unknown>;
		placeholder?: Record<string, unknown> | null;
	}>(),
	{
		modelValue: () => {
			return {};
		},
		placeholder: null,
	},
);

const emit = defineEmits(['update:modelValue']);

const { useOptionsSchemas } = window.zb.composables;

const valueModel = computed({
	get() {
		return props.modelValue || {};
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});

const computedSchema = computed(() => {
	const { getSchema } = useOptionsSchemas();
	const schema = getSchema('typography');

	if (props.placeholder) {
		const newSchema: Record<string, unknown> = {};
		Object.keys(schema).forEach(optionID => {
			const childSchema = schema[optionID];
			if (props.placeholder && typeof props.placeholder[optionID] !== 'undefined') {
				childSchema.placeholder = props.placeholder[optionID];
			}

			newSchema[optionID] = childSchema;
		});
		return newSchema;
	} else {
		return schema;
	}
});
</script>

<style lang="scss">
.znpb-options-form-wrapper.znpb-option__typography-wrapper {
	flex-grow: 1;
	padding: 0;
}

.znpb-input-type--typography {
	padding: 0;
}
</style>
