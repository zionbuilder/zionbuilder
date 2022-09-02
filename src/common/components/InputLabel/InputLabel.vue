<template>
	<label
		class="znpb-form-label"
		:class="{
			[`znpb-form-label--${align}`]: align,
			[`znpb-form-label--position-${position}`]: position,
		}"
	>
		<!-- @slot text -->
		<slot />
		<div v-if="$slots.label || label || icon" class="znpb-form-label-content">
			<Icon v-if="icon" :icon="icon" />

			<h4 v-if="!$slots.label">
				{{ label }}
			</h4>
			<!-- @slot Label text -->
			<slot name="label"></slot>
		</div>
	</label>
</template>

<script lang="ts">
export default {
	name: 'InputLabel',
};
</script>

<script lang="ts" setup>
import { Icon } from '../Icon';

withDefaults(
	defineProps<{
		label?: string;
		align?: 'right' | 'left' | 'center';
		position?: 'right' | 'left' | 'bottom';
		icon?: string;
	}>(),
	{
		align: 'center',
		position: 'bottom',
	},
);
</script>

<style lang="scss">
.znpb-form-label {
	display: flex;
	flex-direction: column;

	&-content {
		margin-top: 10px;
		font-size: 10px;
		text-transform: uppercase;

		h4 {
			margin: 0;
			color: var(--zb-surface-icon-color);
			font-size: 10px;
			font-weight: 700;
			letter-spacing: 1px;
		}
	}

	&--center {
		align-items: center;
	}

	&--left {
		align-items: left;
	}

	&--right {
		align-items: right;
	}

	&--position-left {
		flex-direction: row-reverse;

		.znpb-form-label-content {
			margin: 0 10px;
		}
	}

	&--position-right {
		flex-direction: row;
	}

	&--position-right,
	&--position-left {
		.znpb-form-label-content {
			margin: 0 10px;
		}
	}
}
</style>
