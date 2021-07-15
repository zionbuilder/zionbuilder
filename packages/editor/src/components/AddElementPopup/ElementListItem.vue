<template>
	<li
		class="znpb-element-box"
		ref="elementBox"
		:class="['znpb-element-box--' + item.element_type]"
	>
		<span
			v-if="label"
			class="znpb-element-box__label"
			:style="{ background: label.color }"
		>{{label.text}}</span>

		<ElementListItemSVG
			v-if="isSVG"
			:svg="get_element_image"
		/>

		<img
			v-else-if="get_element_image"
			:src="get_element_image"
			class="znpb-element-box__image"
		/>

		<Icon
			v-else
			:icon="get_element_icon"
			:size="48"
		/>
		<span class="znpb-element-box__element-name">
			{{ item.name }}
		</span>
	</li>
</template>

<script>
import ElementListItemSVG from './ElementListItemSVG.vue'

export default {
	name: 'ElementListItem',
	components: {
		ElementListItemSVG
	},
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	setup (props) {
		const get_element_image = props.item.thumb ? props.item.thumb : null
		const isSVG = get_element_image ? get_element_image.indexOf('.svg') !== -1 : false
		const get_element_icon = props.item.icon ? props.item.icon : 'element-default'
		const label = props.item.label

		return {
			isSVG,
			get_element_image,
			get_element_icon,
			label
		}
	}
}
</script>

<style lang="scss">
/* vars */

.znpb-element-box {
	position: relative;
	display: flex;
	flex-direction: column;
	justify-content: flex-start;
	align-items: center;
	cursor: pointer;
	user-select: none;

	&__label {
		position: absolute;
		top: 10px;
		right: 10px;
		padding: 3px 5px;
		color: #fff;
		font-size: 8px;
		font-weight: 700;
		background: var(--zb-pro-color);
		border-radius: 2px;
	}

	.znpb-editor-icon-wrapper, .znpb-element-box__image {
		width: 100%;
		margin-bottom: 5px;
		color: var(--zb-surface-icon-color);
		background-color: var(--zb-surface-color);
		box-shadow: 0 5px 10px 0 var(--zb-surface-shadow);
		border: 1px solid var(--zb-elements-border-color);
		border-radius: 3px;
		transition: all .2s;

		&::after {
			content: "";
			display: block;
			padding-top: 100%;
		}
	}

	.znpb-element-box__image {
		padding: 27px;
	}

	&__element-name {
		color: var(--zb-surface-text-color);
		font-size: 12px;
		font-weight: 500;
		line-height: 1.5;
		text-align: center;
	}

	&:hover {
		.znpb-editor-icon-wrapper, .znpb-element-box__image {
			color: var(--zb-surface-text-hover-color);
		}
		.znpb-editor-icon-wrapper {
			box-shadow: 0 5px 10px 0 var(--zb-surface-shadow-hover);
		}
	}
}
</style>
