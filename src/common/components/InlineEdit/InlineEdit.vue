<template>
	<div :contenteditable="enabled" spellcheck="false" @input="onInput">
		{{ modelValue }}
	</div>
</template>

<script lang="ts">
export default {
	name: 'InlineEdit',
};
</script>

<script lang="ts" setup>
withDefaults(
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

function onInput(event: Event) {
	emit('update:modelValue', (event.target as HTMLDivElement).innerText);
}
</script>
