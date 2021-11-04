<template>
	<li
		class="znpb-element-box"
		ref="elementBox"
		:class="['znpb-element-box--' + item.element_type]"
	>

		<span
			v-if="item.label"
			class="znpb-element-box__label"
			:style="{ background: item.label.color }"
		>{{item.label.text}}</span>

		<UIElementIcon
			:element="item"
			class="znpb-element-box__icon"
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
	}
}
</script>

<style lang="scss">
.znpb-element-box {
	position: relative;
	display: flex;
	flex-direction: column;
	justify-content: flex-start;
	align-items: center;
	min-width: 0;
	cursor: pointer;
	user-select: none;

	&__label {
		position: absolute;
		top: 8px;
		right: 8px;
		padding: 2px 3px;
		color: #000;
		font-size: 7px;
		font-weight: 700;
		background: var(--zb-pro-color);
		border-radius: 2px;
	}

	.znpb-editor-icon-wrapper, .znpb-element-box__image {
		width: 100%;
		margin-bottom: 5px;
		color: var(--zb-surface-text-color);
		background-color: var(--zb-surface-lighter-color);
		border-radius: 4px;
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
		font-size: 11px;
		font-weight: 500;
		line-height: 1.3;
		text-align: center;
		max-width: 100%;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}

	&:hover {
		.znpb-editor-icon-wrapper, .znpb-element-box__image {
			color: var(--zb-surface-text-active-color);
		}

		.znpb-element-box__icon {
			background-color: var(--zb-surface-lightest-color);
			box-shadow: 0 4px 20px 0 var(--zb-surface-shadow-hover);
		}
	}
}

.znpb-element-box__icon {
	font-size: 36px;
}
</style>
