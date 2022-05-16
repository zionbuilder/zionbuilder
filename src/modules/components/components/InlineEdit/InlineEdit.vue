<template>
	<div :contenteditable="enabled" spellcheck="false" @input="onInput">
		{{ editedText }}
	</div>
</template>

<script lang="ts">
export default {
	name: 'InlineEdit',
};
</script>

<script lang="ts" setup>
import { ref, watch } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		enabled?: boolean;
	}>(),
	{
		modelValue: '',
		enabled: true,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();
const editedText = ref(props.modelValue);
const newText = ref(props.modelValue);

watch(
	() => props.modelValue,
	newValue => {
		if (newValue !== newText.value) {
			editedText.value = newValue;
		}
	},
);

function onInput(event: Event) {
	newText.value = (event.target as HTMLDivElement).innerText;
	emit('update:modelValue', (event.target as HTMLDivElement).innerText);
}
</script>
