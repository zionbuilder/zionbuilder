<template>
	<div>
		<slot name="start" />

		<span v-if="options.show_dismiss" class="zb-el-alert__closeIcon"> </span>

		<span v-if="options.title" class="zb-el-alert__title" v-html="options.title"> </span>

		<RenderValue v-if="options.description" html-tag="div" option="description" class="zb-el-alert__description" />

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'Alert',
	props: ['options', 'element', 'api'],
};
</script>

<style lang="scss">
.zb-el-alert__description {
	.znpb-inline-text-editor * {
		margin: 0;
	}
}
</style>
