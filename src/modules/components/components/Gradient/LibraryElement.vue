<template>
	<div
		class="znpb-form-library-inner-pattern-wrapper"
		:class="{
			'znpb-form-library-inner-pattern-wrapper--start': onstart,
			'znpb-form-library-inner-pattern-wrapper--stretch': !expand,
			'znpb-form-library-inner-pattern-wrapper--expand': expand,
			'znpb-form-library-inner-pattern-wrapper--hasInput': hasInput,
		}"
	>
		<template v-if="!hasInput">
			<Icon
				v-if="animation"
				icon="more"
				class="znpb-form-library-inner-action-icon"
				@click.stop="(expand = !expand), (onstart = false)"
			/>
			<Icon v-if="icon" :icon="icon" class="znpb-form-library-inner-action-icon" @click.stop="$emit('close-library')" />
		</template>
		<slot></slot>
	</div>
</template>

<script lang="ts">
export default {
	name: 'LibraryElement',
};
</script>

<script lang="ts" setup>
import { ref } from 'vue';
import { Icon } from '../Icon';

withDefaults(
	defineProps<{
		animation?: boolean;
		icon?: string;
		hasInput?: boolean;
	}>(),
	{
		animation: true,
	},
);

defineEmits(['close-library']);

const onstart = ref(true);
const expand = ref(false);
</script>

<style lang="scss">
.znpb-form-library-inner {
	&-pattern-wrapper {
		overflow: hidden;
		width: 100%;
		background: var(--zb-surface-color);
		border-top: 2px solid var(--zb-surface-border-color);
		transition: height 0.2s ease-in-out;

		.znpb-tabs--minimal {
			.znpb-tabs__header > .znpb-tabs__header-item {
				flex-grow: 0;
				padding: 0;
				line-height: 1;
				background: transparent;
			}
			.znpb-tabs__content {
				background: transparent;
			}
		}

		// fixed heigth to animate the patterns
		&.znpb-form-library-inner-pattern-wrapper--stretch {
			width: auto;
			transform: translateY(0);
			animation-duration: 0.2s;
			animation-name: stretchPattern;
			animation-timing-function: ease-out;
		}
		&.znpb-form-library-inner-pattern-wrapper--expand {
			overflow: visible;
			width: auto;
			min-height: 100%;
			transform: translateY(-61%);
			animation-duration: 0.2s;
			animation-name: expandPattern;
			animation-timing-function: ease-out;

			.znpb-form-library-grid__panel-content {
				max-height: 161px;
			}
		}
		&.znpb-form-library-inner-pattern-wrapper--start {
			position: relative;
			width: auto;
			animation: none;
		}
		&--hasInput.znpb-form-library-inner-pattern-wrapper--expand,
		&--hasInput.znpb-form-library-inner-pattern-wrapper--stretch {
			background: #fff;
			border-top: 1px solid var(--zb-surface-border-color);
			transition: none;
		}

		.znpb-admin-small-loader {
			top: 50%;
		}

		.znpb-tabs > .znpb-tabs__header {
			justify-content: center;
			padding: 10px;
		}
		.znpb-tabs__header-item {
			padding: 0;
			color: var(--zb-surface-text-color);
			font-size: 10px;

			&:hover {
				color: var(--zb-surface-text-hover-color);
				background: transparent;
			}
			&--active {
				color: var(--zb-surface-text-active-color);
			}
			&:first-child {
				padding: 0;
				margin-right: 5px;
			}
			&:last-child {
				padding: 0;
				margin-right: 0;
			}
		}
		.znpb-tabs__content {
			background: transparent;
		}

		.znpb-loader-wrapper {
			padding: 0 0 12px;
		}
	}

	&-action-icon {
		position: absolute;
		top: 9px;
		right: 20px;
		color: var(--zb-surface-text-hover-color);
		cursor: pointer;
	}
}

@keyframes stretchPattern {
	0% {
		transform: translateY(-61%);
	}
	100% {
		transform: translateY(0);
	}
}
@keyframes expandPattern {
	0% {
		transform: translateY(0);
	}
	100% {
		transform: translateY(-61%);
	}
}
</style>
