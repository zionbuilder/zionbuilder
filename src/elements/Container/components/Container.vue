<template>
	<SortableContent :element="element" :tag="htmlTag" v-bind="extraAttributes">
		<template #start>
			<slot name="start" />
		</template>

		<template #end>
			<slot name="end" />
		</template>
	</SortableContent>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: {
		link?: {
			link: string;
			target: string;
			title: string;
		};
		tag?: string;
	};
	element: ZionElement;
	api: ZionElementRenderApi;
}>();

const htmlTag = computed(() => {
	if (props.options.link && props.options.link.link) {
		return 'a';
	}
	return props.options.tag && /^[a-z0-9]+$/i.test(props.options.tag) ? props.options.tag : 'div';
});

const extraAttributes = computed(() => window.zb.utils.getLinkAttributes(props.options.link));
</script>
