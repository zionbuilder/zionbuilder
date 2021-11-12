<template>
	<div class="znpb-editor-shapeWrapper">
		<slot></slot>
		<SvgMask
			v-if="shapePath"
			:shapePath="shapePath"
			:position="position"
		>
		</SvgMask>
	</div>
</template>

<script>
import SvgMask from './SvgMask.vue'

export default {
	name: 'Shape',
	components: {
		SvgMask
	},
	props: {
		/**
		 * Value for input
		 */
		shapePath: {
			required: false
		},
		position: {
			type: String,
			required: false
		}
	}
}
</script>
<style lang="scss">
.znpb-shape-divider-icon {
	width: 100%;
	color: var(--zb-surface-color);

	svg {
		display: block;

		fill: currentColor;
	}
}
.znpb-editor-shapeWrapper {
	position: relative;
	display: flex;
	align-items: flex-end;
	min-height: 72px;
	margin-bottom: 18px;
	color: var(--zb-surface-color);
	background-color: var(--zb-secondary-color);
	border-radius: 3px;
	transition: box-shadow .2s linear;
	cursor: pointer;

	&:last-of-type {
		margin-bottom: 0;
	}

	&:hover {
		box-shadow: 0 2px 15px 0 var(--zb-surface-shadow);
	}

	&.znpb-active-shape-preview {
		background-color: var(--zb-surface-color);
		.znpb-empty-list__container {
			width: 100%;
		}
		&.mask-active {
			background-color: var(--zb-secondary-color);
		}
	}

	.zb-mask {
		color: var(--zb-surface-light-color);
	}

	&.mask-active {
		.zb-mask {
			color: var(--zb-surface-color);
		}

		.znpb-active-shape-preview__action {
			position: absolute;
			top: 10px;
			right: 10px;
			z-index: 9;
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
