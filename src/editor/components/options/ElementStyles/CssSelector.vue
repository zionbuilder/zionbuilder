<template>
	<div class="znpb-css-class-selector__item" :class="{ 'znpb-css-class-selector__item--selected': isSelected }">
		<div class="znpb-css-class-selector__item-content">
			<span
				class="znpb-css-class-selector__item-type"
				:class="{ [`znpb-css-class-selector__item-type--${type}`]: type }"
				>{{ type }}
			</span>
			<span class="znpb-css-class-selector__item-name">
				<span :title="name">{{ name }}</span>
			</span>
		</div>

		<Icon
			v-if="showActions"
			v-znpb-tooltip="__('Copy styles', 'zionbuilder')"
			icon="copy"
			class="znpb-css-class-selector__item-copy"
			@click.stop="emit('copy-styles')"
		/>

		<Icon
			v-if="showActions"
			v-znpb-tooltip="__('Paste styles', 'zionbuilder')"
			icon="paste"
			class="znpb-css-class-selector__item-paste"
			:class="{
				'znpb-css-class-selector__item-paste--disabled': !cssClasses.copiedStyles,
			}"
			@click.stop="emit('paste-styles')"
		/>

		<Icon
			v-if="showActions"
			v-znpb-tooltip="__('Remove class from element.', 'zionbuilder')"
			icon="close"
			class="znpb-css-class-selector__item-close"
			:class="{
				'znpb-css-class-selector__item-close--disabled': !showDelete,
			}"
			@click.stop="emit('remove-class')"
		/>
	</div>
</template>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { useCSSClassesStore } from '/@/editor/store';

withDefaults(
	defineProps<{
		name: string;
		type: string;
		isSelected?: boolean;
		showDelete?: boolean;
		showActions?: boolean;
	}>(),
	{
		isSelected: false,
		showDelete: false,
		showActions: true,
		showChangesBullet: false,
	},
);

const emit = defineEmits(['remove-class', 'copy-styles', 'paste-styles', 'remove-extra-classes']);

const cssClasses = useCSSClassesStore();
</script>

<style lang="scss">
.znpb-class-selector {
	.znpb-css-class-selector__item-content {
		width: 100%;
	}

	.znpb-css-class-selector__item-name {
		position: relative;
		overflow: hidden;
		width: 100%;
		height: 100%;

		> span {
			position: absolute;
			white-space: pre;
		}
	}
}

.znpb-class-selector__popper .znpb-css-class-selector__item {
	margin: 0 -15px;

	&-name {
		word-break: break-all;
	}

	&:hover {
		background-color: var(--zb-surface-lighter-color);

		.znpb-css-class-selector__item-name {
			color: var(--zb-surface-text-active-color);
		}
	}
}

.znpb-css-class-selector__item {
	display: flex;
	justify-content: space-between;
	align-items: stretch;
	padding: 9px 15px;
	cursor: pointer;

	&:hover {
		color: var(--zb-surface-text-active-color);
	}

	&-close {
		margin-left: 8px;

		.zion-icon {
			font-size: 10px;
		}

		&:hover .zion-icon {
			opacity: 1;
		}
	}

	&-content {
		display: flex;
		align-items: center;
		width: 100%;
	}

	&--selected {
		color: var(--zb-surface-active-color);
		background-color: var(--zb-surface-lighter-color);
		transition: all 0.22s ease-out;
	}

	&--selected &-name {
		color: var(--zb-surface-text-active-color);
	}

	&-type {
		padding: 3px 6px;
		color: var(--zb-surface-color);
		font-size: 8px;
		font-weight: 700;
		background-color: var(--zb-success-color);
		border-radius: 2px;

		&--id {
			background-color: var(--zb-secondary-color);
		}

		&--id {
			background-color: var(--zb-column-color);
		}
	}

	&-name {
		display: flex;
		align-items: center;
		flex-grow: 1;
		padding-left: 10px;
		color: var(--zb-surface-text-color);
		font-size: 13px;
		font-weight: 500;
		line-height: 1.4;
	}

	&-close {
		display: flex;
		color: var(--zb-surface-icon-color);
		font-size: 8px;
		cursor: pointer;

		&:hover {
			color: var(--zb-surface-text-hover-color);
		}

		&--disabled {
			opacity: 0.35;
			pointer-events: none;
		}
	}

	&-copy,
	&-paste {
		padding: 2px 4px;
		color: var(--zb-surface-icon-color);

		&:hover {
			color: var(--zb-surface-text-hover-color);
		}

		&--disabled {
			opacity: 0.35;
			pointer-events: none;
		}
	}
}
</style>
