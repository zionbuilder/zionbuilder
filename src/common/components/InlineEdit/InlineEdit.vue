<template>
	<input v-model="computedModelValue" class="znpb-inlineEditInput" />
</template>

<script lang="ts">
export default {
	name: 'InlineEdit',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		enabled?: boolean;
		tag?: string;
	}>(),
	{
		modelValue: '',
		enabled: true,
		tag: 'div',
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

const computedModelValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});
</script>
<style lang="scss">
.znpb-inlineEditInput {
	border: 0;
	background: transparent;
	color: inherit;
	font-size: inherit;
}
</style>
