<template>
	<div>
		<slot name="start" />
		<!-- eslint-disable-next-line vue/no-v-html -->
		<div v-html="options.content"></div>
		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
defineProps<{
	options: {
		content: string;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();
</script>
