<template>
	<div
		class="znpb-gradient-element"
		:class="{'znpb-gradient-element--active': isActive}"
	>
		<OneGradient
			:round="true"
			:config="localConfig"
			@click.native="$emit('change-active-gradient', config)"
		/>
		<BaseIcon
			icon="close"
			v-if="showRemove"
			@click.native.stop="$emit('delete-gradient')"
		/>
	</div>

</template>
<script>
import BaseIcon from '../BaseIcon.vue'
import OneGradient from './OneGradient.vue'

export default {
	name: 'GradientElement',
	components: {
		OneGradient,
		BaseIcon
	},
	props: {
		config: {
			type: Object,
			required: false
		},
		showRemove: {
			type: Boolean,
			required: false,
			default: true
		},
		isActive: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	computed: {
		localConfig: {
			get () {
				return this.config
			},
			set (newConfig) {
				this.localConfig = newConfig
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-gradient-element {
	position: relative;
	width: 46px;
	height: 46px;
	margin-right: 10px;
	margin-bottom: 10px;

	&--active {
		&:before {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			box-shadow: 0 0 0 2px $border-color;
			border-radius: 3px;
		}
		.znpb-gradient-preview-transparent {
			position: relative;
			top: 2px;
			left: 2px;
			width: calc(100% - 4px);
			height: calc(100% - 4px);
			border-radius: 2px;
		}

		.znpb-gradient-preview {
			border-radius: 2px !important;
		}
	}

	.znpb-gradient-preview {
		width: 100%;
		height: 100%;
		margin-bottom: 0;
		border-radius: 3px;
	}

	.znpb-editor-icon-wrapper {
		position: absolute;
		top: -5px;
		right: -5px;
		width: 18px;
		height: 18px;
		transition: opacity .15s, visibility .15s;
		cursor: pointer;
		opacity: 0;
		visibility: hidden;

		&::after {
			content: "";
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: $surface;
			box-shadow: 0 0 0 1px $border-color;
			border-radius: 50%;
			transform: scale(.8);
			transition: transform .15s;
		}

		.zion-icon.zion-svg-inline {
			z-index: 1;
			width: 8px;
			margin: 0 auto;
		}
	}

	&:hover {
		.znpb-editor-icon-wrapper {
			opacity: 1;
			visibility: visible;

			&::after {
				transform: scale(1);
			}
		}
	}
}
</style>
