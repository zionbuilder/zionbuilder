<!-- eslint-disable vue/no-v-html -->
<template>
	<div class="znpb-option__html" v-html="content"></div>
</template>

<script lang="ts" setup>
withDefaults(
	defineProps<{
		content?: string;
	}>(),
	{
		content: '',
	},
);
</script>

<script lang="ts">
export default {
	name: 'HTML',
};
</script>
