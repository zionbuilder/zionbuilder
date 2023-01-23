<template>
	<SortableContent class="zb-column" :element="element" :tag="htmlTag" v-bind="extraAttributes">
		<template #start>
			<slot name="start" />
			<SvgMask
				v-if="topMask !== undefined && topMask.shape"
				:shape-path="topMask['shape']"
				:color="topMask['color']"
				:flip="topMask['flip']"
				position="top"
			/>

			<SvgMask
				v-if="bottomMask !== undefined && bottomMask.shape"
				:shape-path="bottomMask['shape']"
				:color="bottomMask['color']"
				:flip="bottomMask['flip']"
				position="bottom"
			/>
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
		shapes?: {
			top?: {
				shape: string;
				color: string;
				flip: boolean;
			};
			bottom?: {
				shape: string;
				color: string;
				flip: boolean;
			};
		};
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

const extraAttributes = computed(() => {
	return window.zb.utils.getLinkAttributes(props.options.link);
});

const topMask = computed(() => {
	return props.options.shapes?.top;
});

const bottomMask = computed(() => {
	return props.options.shapes?.bottom;
});
</script>
