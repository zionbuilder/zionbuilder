<template>
	<div
		class="znpb-css-class-selector__item"
		:class="{ 'znpb-css-class-selector__item--selected': isSelected }"
	>
		<div class="znpb-css-class-selector__item-content">
			<span
				class="znpb-css-class-selector__item-type"
				:class="{[`znpb-css-class-selector__item-type--${type}`]: type}"
			>{{ type }}
			</span>
			<span class="znpb-css-class-selector__item-name">
				<span :title="name">{{name}}</span>
			</span>

			<ChangesBullet
				v-if="showChangesBullet"
				@remove-styles="$emit('remove-extra-classes')"
				:discard-changes-title="$translate('remove_additional_classes')"
			/>
		</div>

		<Tooltip
			v-if="showDelete"
			:content="$translate('delete_class_tooltip')"
			placement="top"
			class="znpb-css-class-selector__item-close"
		>
			<Icon
				icon="close"
				v-on:click.stop='handleDeleteClass(name)'
			>
			</Icon>
		</Tooltip>

	</div>
</template>

<script>
export default {
	name: 'CssSelector',
	props: {
		name: {
			type: String,
			required: true
		},
		type: {
			type: String,
			required: true,
			default: 'id'
		},
		isSelected: {
			type: Boolean,
			required: false,
			default: false
		},
		showDelete: {
			type: Boolean,
			required: false,
			default: false
		},
		showChangesBullet: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {}
	},
	methods: {
		handleDeleteClass () {
			this.$emit('remove-class', this.name)
		}
	}
}
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
			content: "";
			position: absolute;
			top: 0;
			right: 0;
			z-index: 1;
			width: 20px;
			height: 100%;
			background: linear-gradient(
				90deg,
				rgba(245, 245, 245, 0) 0%,
				var(--zb-input-faded-bg-color) 100%
			);
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
		padding-left: 15px;

		.zion-icon {
			font-size: 10px;
			opacity: 0.5;
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
		padding: 5px 10px;
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
		font-size: 8px;
		cursor: pointer;
	}
}
</style>
