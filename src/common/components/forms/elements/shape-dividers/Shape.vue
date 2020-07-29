<template>
	<div class="znpb-editor-shapeWrapper">
		<slot></slot>
		<div
			v-if="shape !== undefined"
			class="znpb-shape-divider-icon znpb-mask"
			v-html="getSvgIcon"
		>
		</div>
	</div>
</template>

<script>
import Masks from './Masks.vue'
export default {
	name: 'Shape',
	mixins: [Masks],
	props: {
		/**
		 * Value for input
		 */
		shape: {
			type: String,
			required: false
		}
	},
	computed: {
		shapePath () {
			return this.shape || ''
		}
	}

}
</script>
<style lang="scss">
.znpb-shape-divider-icon {
	width: 100%;
	color: #fff;
	svg {
		display: block;
		width: 100%;
		height: auto;
		margin: 0;
	}
}
.znpb-editor-shapeWrapper {
	position: relative;
	display: flex;
	min-height: 72px;
	margin-bottom: 18px;
	color: #fff;
	background-color: rgb(0, 109, 210);
	border-radius: 3px;
	transition: box-shadow .2s linear;
	cursor: pointer;

	&:last-of-type {
		margin-bottom: 0;
	}

	&:hover {
		box-shadow: 0 2px 15px 0 rgba(86, 86, 86, .1);
	}

	&.znpb-active-shape-preview {
		background-color: #fff;
		.znpb-empty-list__container {
			width: 100%;
		}
		&.mask-active {
			background-color: rgb(0, 109, 210);
		}
	}

	&.mask-active {
		box-shadow: 0 5px 10px 0 rgba(86, 86, 86, .2);
		.znpb-active-shape-preview__action {
			position: absolute;
			top: 10px;
			right: 10px;
			display: flex;
			justify-content: center;
			align-items: center;
			width: 20px;
			height: 20px;
			color: #858585;
			background-color: #fff;
			box-shadow: 0 5px 10px 0 rgba(86, 86, 86, .2);
			border-radius: 50%;
		}
	}
}
</style>
