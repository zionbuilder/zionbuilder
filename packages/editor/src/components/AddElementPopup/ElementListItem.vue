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

		<img
			v-if="get_element_image"
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
export default {
	name: 'ElementListItem',
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	setup (props) {
		const get_element_image = props.item.thumb ? props.item.thumb : null
		const get_element_icon = props.item.icon ? props.item.icon : 'element-default'
		const label = props.item.label

		return {
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
		font-size: 9px;
		letter-spacing: .5px;
		background: $column-color;
		border-radius: 2px;
	}

	.znpb-editor-icon-wrapper, .znpb-element-box__image {
		width: 100%;
		margin-bottom: 12px;
		color: darken($surface-variant, 15%);
		background-color: rgb(255, 255, 255);
		box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .08);
		border: 1px solid rgb(241, 241, 241);
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
		color: darken($surface-headings-color, 20%);
		font-size: 12px;
		font-weight: 500;
		line-height: 1.5;
		text-align: center;
	}

	&:hover {
		.znpb-editor-icon-wrapper {
			box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .15);
		}
	}
}
</style>
