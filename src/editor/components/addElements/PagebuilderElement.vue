<template>
	<li
		class="znpb-element-box"
		@click="emitEventbus($event)"
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

		<BaseIcon
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
	name: 'PagebuilderElement',
	props: {
		item: {
			type: Object,
			required: true
		}
	},
	data () {
		return {}
	},
	computed: {
		get_element_image () {
			return this.item.thumb ? this.item.thumb : null
		},
		get_element_icon () {
			return this.item.icon ? this.item.icon : 'element-default'
		},
		label () {
			return this.item.label
		}
	},

	methods: {
		emitEventbus (event) {
			window.ZionBuilderApi.trigger('add-element', this.item)
		}
	},
	mounted () {
		this.$el.zionElement = this.item
	}
}
</script>

<style lang="scss">
/* vars */

.znpb-element-box {
	display: flex;
	flex-direction: column;
	justify-content: flex-start;
	align-items: center;
	cursor: pointer;
	user-select: none;
	position: relative;

	&__label {
		padding: 3px 5px;
		position: absolute;
		top: 10px;
		right: 10px;
		background: $column-color;
		color: #fff;
		border-radius: 2px;
		font-size: 9px;
		letter-spacing: 0.5px;
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
			content: '';
			display: block;
			padding-top: 100%;
		}
	}

	&__element-name {
		color: darken($surface-headings-color, 20%);
		font-size: 12px;
		font-weight: 500;
		text-align: center;
	}

	&:hover {
		.znpb-editor-icon-wrapper {
			box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .15);
		}
	}
}
</style>
