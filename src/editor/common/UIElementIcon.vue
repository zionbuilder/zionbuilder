<template>
	<ElementListItemSVG v-if="isSVG" :svg="get_element_image" />

	<img v-else-if="get_element_image" :src="get_element_image" class="znpb-element-box__image" />

	<Icon v-else :icon="get_element_icon" :size="size" />
</template>

<script lang="ts" setup>
import ElementListItemSVG from './ElementListItemSVG.vue';

const props = withDefaults(
	defineProps<{
		element: ZionElementDefinition;
		size?: number;
	}>(),
	{
		size: 36,
	},
);

const get_element_image = props.element.thumb ? props.element.thumb : null;
const isSVG = get_element_image ? get_element_image.indexOf('.svg') !== -1 : false;
const get_element_icon = props.element.icon ? props.element.icon : 'element-default';
</script>

<style></style>
