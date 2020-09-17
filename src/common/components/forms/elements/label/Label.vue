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
		<div
			class="znpb-form-label-content"
			v-if="$slots.label || label || icon"
		>
			<BaseIcon
				v-if="icon"
				:icon="icon"
			/>

			<h4 v-if="!$slots.label">
				{{label}}
			</h4>
			<!-- @slot Label text -->
			<slot name="label"></slot>
		</div>
	</label>
</template>

<script>
import BaseIcon from '@/common/components/BaseIcon.vue'

export default {
	name: 'InputLabel',
	components: {
		BaseIcon
	},
	props: {
		/**
		 * Label string
		 */
		label: {
			type: String,
			required: false
		},
		/**
		 * Alignment
		 */
		align: {
			type: String,
			required: false,
			default: 'center'
		},
		/**
		 * Position
		 */
		position: {
			type: String,
			required: false,
			default: 'bottom'
		},
		/**
		 * Position
		 */
		icon: {
			type: String,
			required: false
		}
	}
}
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
			color: $surface-headings-color;
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

	&--position-right, &--position-left {
		.znpb-form-label-content {
			margin: 0 10px;
		}
	}
}
</style>
