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
				<span>{{name}}</span>
			</span>
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
		width: 100%;
		height: 100%;
		overflow: hidden;

		&::before {
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			width: 20px;
			height: 100%;
			background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 100%);
			z-index: 1;
		}

		> span {
			position: absolute;
		}
	}
}

.znpb-class-selector__popper .znpb-css-class-selector__item {
	margin: 0 -15px;

	&-name {
		word-break: break-all;
	}
}

.znpb-css-class-selector__item {
	display: flex;
	justify-content: space-between;
	align-items: stretch;
	padding: 9px 15px;
	cursor: pointer;

	&:hover {
		color: $surface-active-color;
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
		padding-left: 10px;
		color: darken($font-color, 10%);
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
