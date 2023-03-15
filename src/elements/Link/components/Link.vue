<template>
	<a v-bind="extraAttributes">
		<slot name="start" />
		{{ options.content }}
		<slot name="end" />
	</a>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		content: string;
		link: {
			link: string;
			target: string;
			title: string;
		};
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const extraAttributes = computed(() => window.zb.utils.getLinkAttributes(props.options.link));
</script>
