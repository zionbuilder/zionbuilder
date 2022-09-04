<template>
	<component :is="tag" :contenteditable="enabled" spellcheck="false" @input="onInput">
		{{ modelValue }}
	</component>
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

function onInput(event: Event) {
	emit('update:modelValue', (event.target as HTMLDivElement).innerText);
}
</script>
