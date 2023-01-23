<!-- Not used component -->
<template>
	<div class="znpb-input-border-radius-wrapper">
		<InputLabel
			v-if="title.length"
			:label="title"
			class="znpb-typography-group-item znpb-typography-group-item-font-weight"
		>
			<InputNumberUnit v-model="computedValue" :min="0" :max="999" :step="1" default-unit="px" />
		</InputLabel>
	</div>
</template>
<script lang="ts">
export default {
	name: 'InputBorderRadius',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import { InputNumberUnit } from '../InputNumber';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		title?: string;
	}>(),
	{
		modelValue: '',
		title: '',
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

const computedValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: string) {
		emit('update:modelValue', newValue);
	},
});
</script>
<style lang="scss">
.znpb-input-border-radius-wrapper {
	margin-bottom: 15px;
	.znpb-form-label {
		display: flex;
		flex-direction: row-reverse;
		justify-content: space-between;
	}
}
</style>
