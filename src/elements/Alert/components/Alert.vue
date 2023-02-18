<template>
	<div>
		<slot name="start" />

		<span v-if="options.show_dismiss" class="zb-el-alert__closeIcon"> </span>

		<!-- eslint-disable-next-line vue/no-v-html -->
		<span v-if="options.title" class="zb-el-alert__title" v-html="options.title"> </span>

		<RenderValue v-if="options.description" html-tag="div" option="description" class="zb-el-alert__description" />

		<slot name="end" />
	</div>
</template>

<script lang="ts" setup>
defineProps<{
	options: {
		title: string;
		description: string;
		show_dismiss: boolean;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();
</script>

<style lang="scss">
.zb-el-alert__description {
	.znpb-inline-text-editor * {
		margin: 0;
	}
}
</style>
