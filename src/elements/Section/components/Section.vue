<template>
	<component :is="htmlTag" class="zb-section">
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

		<SortableContent
			v-bind="api.getAttributesForTag('inner_content_styles')"
			:element="element"
			class="zb-section__innerWrapper"
			:class="api.getStyleClasses('inner_content_styles')"
		></SortableContent>

		<slot name="end" />
	</component>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps<{
	options: Object;
	api: Object;
	element: Object;
}>();

const shapes = computed(() => props.options?.shapes || {});

const topMask = computed(() => shapes.value.top);
const bottomMask = computed(() => shapes.value.bottom);
const htmlTag = computed(() => props.options?.tag || 'section');
</script>

<script lang="ts">
export default {
	name: 'ZionSection',
};
</script>
