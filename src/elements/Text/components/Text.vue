<template>
	<div>
		<slot name="start" />
		<RenderValue option="content" />
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
