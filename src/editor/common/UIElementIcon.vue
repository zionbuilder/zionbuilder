<template>
	<ElementListItemSVG v-if="isSVG" :svg="get_element_image" />

	<img v-else-if="get_element_image" :src="get_element_image" class="znpb-element-box__image" />

	<Icon v-else :icon="get_element_icon" :size="size" />
</template>

<script>
import ElementListItemSVG from './ElementListItemSVG.vue';

export default {
	name: 'UIElementIcon',
	components: {
		ElementListItemSVG,
	},
	props: {
		element: {
			type: Object,
			required: true,
		},
		size: {
			type: Number,
			default: 36,
		},
	},
	setup(props) {
		const get_element_image = props.element.thumb ? props.element.thumb : null;
		const isSVG = get_element_image ? get_element_image.indexOf('.svg') !== -1 : false;
		const get_element_icon = props.element.icon ? props.element.icon : 'element-default';

		return {
			isSVG,
			get_element_image,
			get_element_icon,
		};
	},
};
</script>

<style></style>
