<template>
	<div class="znpb-button" :class="{ ['znpb-button--' + type]: type }">
		<!-- @slot Content that will be added inside button -->
		<slot></slot>
	</div>
</template>

<script lang="ts">
export default {
	name: 'Button',
};
</script>

<script lang="ts" setup>
interface IProps {
	type?: string;
}

defineProps<IProps>();
</script>

<style lang="scss">
.znpb-button {
	display: inline-flex;
	justify-content: center;
	align-items: center;
	padding: 13.5px 20px;
	color: var(--zb-primary-text-color);
	font-family: var(--zb-font-stack);
	font-size: 13px;
	font-weight: 500;
	line-height: 1;
	background: var(--zb-primary-color);
	border-radius: 3px;
	transition: all 0.3s;
	cursor: pointer;
	user-select: none;

	&:hover {
		background: var(--zb-primary-hover-color);
	}

	b {
		font-weight: 700;
	}

	&:last-child {
		margin-right: 0;
	}

	&--small {
		padding: 8px 14px;
	}

	&--link {
		font-weight: 400;
		color: var(--zb-surface-text-color);
		background-color: transparent;
		border: 0;

		&:hover {
			color: var(--zb-surface-text-hover-color);
			background: transparent;
		}
	}

	&--minimal {
		padding: 0 40px;
		color: var(--zb-surface-text-active-color);
		font-size: 14px;
		font-weight: 400;
		line-height: 44px;
		background: transparent;

		&:hover {
			background: transparent;
		}
	}

	&--secondary {
		color: var(--zb-secondary-text-color);
		background: var(--zb-secondary-color);

		&:hover,
		&:focus,
		&:active {
			color: var(--zb-secondary-text-color);
		}

		&:hover {
			background: var(--zb-secondary-hover-color);
		}

		&.znpb-button--disabled:hover {
			color: var(--zb-secondary-text-color);
			background: var(--zb-secondary-color);
		}
	}

	&--line {
		padding: 11.5px 20px;
		color: var(--zb-surface-text-active-color);
		background: transparent;
		border: 2px solid var(--zb-surface-border-color);

		& .znpb-editor-icon {
			color: var(--zb-surface-text-color);
		}

		&:hover {
			background: none;
			opacity: 0.6;
		}

		&.znpb-button--disabled:hover {
			opacity: 1;
		}
	}

	&--color {
		padding: 18px 70px;
		background: var(--zb-pro-color);
		&:hover {
			background: var(--zb-pro-color);
		}
	}

	&--danger {
		background: #e35364;

		&:hover {
			background: darken(#e35364, 3);
		}
	}

	&--gray {
		color: var(--zb-surface-text-color);
		background: var(--zb-surface-lighter-color);

		&:hover {
			background: var(--zb-surface-lightest-color);
		}

		&.znpb-button--disabled:hover {
			background: var(--zb-surface-lightest-color);
		}
	}

	&--disabled {
		opacity: 0.5;
		pointer-events: none;
		cursor: not-allowed;
	}

	.znpb-loader::after {
		border-right-color: currentColor;
		border-bottom-color: currentColor;
	}
}
</style>
