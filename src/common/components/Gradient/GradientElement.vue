<template>
	<div class="znpb-gradient-element" :class="{ 'znpb-gradient-element--active': isActive }">
		<OneGradient :round="true" :config="localConfig" @click="$emit('change-active-gradient', config)" />
		<Icon v-if="showRemove" icon="close" @click.stop="$emit('delete-gradient')" />
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientElement',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import Icon from '../Icon/Icon.vue';
import OneGradient from './OneGradient.vue';
import type { Gradient } from './GradientBar.vue';

const props = withDefaults(
	defineProps<{
		config: Gradient;
		showRemove?: boolean;
		isActive?: boolean;
	}>(),
	{
		showRemove: true,
	},
);

defineEmits<{
	(e: 'change-active-gradient', value: Gradient): void;
	(e: 'delete-gradient'): void;
}>();

const localConfig = computed({
	get() {
		return props.config;
	},
	set(newConfig: Gradient) {
		localConfig.value = newConfig;
	},
});
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
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			box-shadow: 0 0 0 2px var(--zb-surface-border-color);
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
		transition: opacity 0.15s, visibility 0.15s;
		cursor: pointer;
		opacity: 0;
		visibility: hidden;

		&::after {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: var(--zb-surface-color);
			box-shadow: 0 0 0 1px var(--zb-surface-border-color);
			border-radius: 50%;
			transform: scale(0.8);
			transition: transform 0.15s;
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
