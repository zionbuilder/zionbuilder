<template>
	<OptionsForm v-model="valueModel" :schema="computedSchema" class="znpb-option__typography-wrapper" />
</template>

<script>
import { computed } from 'vue';

const { useOptionsSchemas } = window.zb.composables;

export default {
	name: 'Typography',
	props: {
		modelValue: {
			type: Object,
			default() {
				return {};
			},
		},
		placeholder: {
			type: Object,
			required: false,
			default: null,
		},
	},
	setup(props) {
		const computedSchema = computed(() => {
			const { getSchema } = useOptionsSchemas();
			const schema = getSchema('typography');

			if (props.placeholder) {
				const newSchema = {};
				Object.keys(schema).forEach(optionID => {
					const childSchema = schema[optionID];
					if (typeof props.placeholder[optionID] !== 'undefined') {
						childSchema.placeholder = props.placeholder[optionID];
					}

					newSchema[optionID] = childSchema;
				});
				return newSchema;
			} else {
				return schema;
			}
		});

		return {
			computedSchema,
		};
	},
	computed: {
		valueModel: {
			get() {
				return this.modelValue || {};
			},
			set(newValue) {
				this.$emit('update:modelValue', newValue);
			},
		},
	},
};
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
