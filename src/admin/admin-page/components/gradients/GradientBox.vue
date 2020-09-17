<template>
	<div
		class="znpb-admin-gradient-preset-box"
		@mouseover="showLink=true"
		@mouseleave="showLink=false"
	>
		<Tooltip
			tag="span"
			class="znpb-admin-gradient-preset-box__delete"
			:content="$translate('delete_gradient_from_preset')"
			@click.native.stop="$emit('delete-gradient')"
		>
			<BaseIcon
				icon="close"
			/>
		</Tooltip>
		<div
			class="znpb-admin-gradient-preset-box__gradient"
		>
			<GradientPreview
				:config="config"
				:round="true"
			/>
		</div>
	</div>
</template>

<script>
import { GradientPreview, BaseIcon, Tooltip } from '@zb/components'

export default {
	name: 'GradientBox',
	components: {
		GradientPreview,
		Tooltip
	},
	props: {
		config: {
			type: Array,
			required: true
		}
	},
	data () {
		return {
			showLink: false
		}
	}
}
</script>

<style lang="scss">
.znpb-admin-gradient-preset-box {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .1);
	border: 1px solid $surface-variant;
	border-radius: 3px;
	cursor: pointer;

	&::before {
		content: "";
		display: block;
		padding-top: 100%;
	}

	&__gradient {
		position: relative;
	}

	&__delete {
		position: absolute;
		top: 10px;
		right: 10px;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 20px;
		height: 20px;
		font-size: 8px;
		border: 1px solid $border-color;
		border-radius: 50%;
		transition: all .15s;
		cursor: pointer;
		opacity: 0;
		visibility: hidden;
	}

	&:hover &__delete {
		opacity: 1;
		visibility: visible;
	}

	.znpb-gradient-preview {
		box-shadow: 0 0 0 2px inset rgba(0, 0, 0, .1);
	}
}
</style>
