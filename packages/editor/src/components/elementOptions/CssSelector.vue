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
				{{name}}
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
.znpb-css-class-selector__item {
	display: flex;
	justify-content: space-between;
	align-items: stretch;
	padding: 9px 12px;
	cursor: pointer;

	&:hover {
		color: $surface-active-color;
	}
	&-close {
		padding-left: 15px;
	}
	&-content {
		display: flex;
		align-items: center;
		width: 100%;
	}

	&--selected {
		color: $surface-active-color;
		background-color: $surface-variant;
		transition: all .22s ease-out;
	}

	&-type {
		padding: 5px 10px;
		color: $surface;
		font-size: 8px;
		font-weight: 700;
		background-color: $green;
		border-radius: 2px;

		&--id {
			background-color: $secondary;
		}

		&--selector {
			background-color: $column-color;
		}
	}

	&-name {
		display: flex;
		align-items: center;
		flex-grow: 1;
		padding-left: 10px;
		color: darken($font-color, 10%);
		font-size: 13px;
		font-weight: 500;
	}

	&-close {
		display: flex;
		font-size: 8px;
		cursor: pointer;
	}
}
</style>
