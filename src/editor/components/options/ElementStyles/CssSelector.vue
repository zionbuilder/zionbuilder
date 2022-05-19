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

			<ChangesBullet
				v-if="showChangesBullet"
				:discard-changes-title="$translate('remove_additional_classes')"
				@remove-styles="$emit('remove-extra-classes')"
			/>
		</div>

		<Icon
			v-if="showActions"
			v-znpb-tooltip="$translate('copy_element_styles')"
			icon="copy"
			class="znpb-css-class-selector__item-copy"
			@click.stop="$emit('copy-styles')"
		/>

		<Icon
			v-if="showActions"
			v-znpb-tooltip="$translate('paste_element_styles')"
			icon="paste"
			class="znpb-css-class-selector__item-paste"
			:class="{
				'znpb-css-class-selector__item-paste--disabled': !copiedStyles,
			}"
			@click.stop="$emit('paste-styles')"
		/>

		<Icon
			v-if="showActions"
			v-znpb-tooltip="$translate('delete_class_tooltip')"
			icon="close"
			class="znpb-css-class-selector__item-close"
			:class="{
				'znpb-css-class-selector__item-close--disabled': !showDelete,
			}"
			@click.stop="handleDeleteClass"
		/>
	</div>
</template>

<script>
import { useCSSClasses } from '../../../composables';

export default {
	name: 'CssSelector',
	props: {
		classConfig: {
			type: Object,
			required: false,
		},
		name: {
			type: String,
			required: true,
		},
		type: {
			type: String,
			required: true,
			default: 'id',
		},
		isSelected: {
			type: Boolean,
			required: false,
			default: false,
		},
		showDelete: {
			type: Boolean,
			required: false,
			default: false,
		},
		showActions: {
			type: Boolean,
			required: false,
			default: true,
		},
		showChangesBullet: {
			type: Boolean,
			required: false,
			default: false,
		},
	},
	setup(props, { emit }) {
		const { copiedStyles } = useCSSClasses();

		function handleDeleteClass() {
			if (props.showDelete) {
				emit('remove-class', props.classConfig.selector);
			}
		}

		return {
			handleDeleteClass,
			copiedStyles,
		};
	},
};
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

		&::before {
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			z-index: 1;
			width: 20px;
			height: 100%;
			background: linear-gradient(90deg, rgba(245, 245, 245, 0) 0%, var(--zb-input-faded-bg-color) 100%);
		}

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

		&--selector {
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
