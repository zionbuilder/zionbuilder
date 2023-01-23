<template>
	<OptionsForm
		v-if="child_options"
		v-model="valueModel"
		class="znpb-option__type-option-group"
		:class="{
			[`znpb-option__type-option-group-layout--${optionsLayout}`]: optionsLayout.length,
		}"
		:schema="child_options"
	/>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue: Record<string, unknown> | undefined;
		// eslint-disable-next-line vue/prop-name-casing
		child_options?: Record<string, unknown>;
		optionsLayout?: string;
	}>(),
	{
		child_options: () => ({}),
		optionsLayout: '',
		modelValue: () => ({}),
	},
);

const emit = defineEmits(['update:modelValue']);

const valueModel = computed({
	get() {
		return props.modelValue || {};
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});
</script>

<style lang="scss">
.znpb-options-form-wrapper.znpb-option__type-option-group {
	flex-wrap: nowrap;
	padding: 0;
	margin: 0 -5px;
}
.znpb-option__type-option-group {
	&-layout {
		&--inline {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
		}

		&--full {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			align-items: flex-start;
		}
	}
}
</style>
