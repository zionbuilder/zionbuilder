<template>
	<OptionsForm v-model="valueModel" :schema="getSchema('typography')" class="znpb-option__typography-wrapper" />
</template>

<script>
import { useOptionsSchemas } from '/@/common/composables';

export default {
	name: 'Typography',
	props: {
		modelValue: {
			type: Object,
			default() {
				return {};
			},
		},
	},
	setup() {
		const { getSchema } = useOptionsSchemas();
		return {
			getSchema,
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
