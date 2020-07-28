<template>

	<div
		class="znpb-form-library-inner-pattern-wrapper"
		:class="{
			'znpb-form-library-inner-pattern-wrapper--start' : onstart,
			'znpb-form-library-inner-pattern-wrapper--stretch' : !expand ,
			'znpb-form-library-inner-pattern-wrapper--expand' : expand,
			'znpb-form-library-inner-pattern-wrapper--hasInput' : hasInput
		}"
	>
		<template v-if="!hasInput">
			<BaseIcon v-if="animation" icon="more" @click.native="expand =! expand, onstart = false" class="znpb-form-library-inner-action-icon"/>
			<BaseIcon  v-if="icon" :icon="icon" @click.native="$emit('close-library')" class="znpb-form-library-inner-action-icon"/>
		</template>
		<slot></slot>
	</div>
</template>
<script>
/**
 * it receives no aimation on beggining
 */

export default {
	name: 'LibraryElement',
	props: {
		animation: {
			type: Boolean,
			required: false,
			default: true
		},
		icon: {
			type: String,
			required: false
		},
		hasInput: {
			type: Boolean,
			required: false,
			default: false
		}
	},

	data () {
		return {
			onstart: true,
			expand: false
		}
	}
}
</script>
<style lang="scss">
.znpb-form-library-inner {
	&-pattern-wrapper {
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

		.znpb-admin-small-loader {
			top: 50%;
		}

		.znpb-tabs > .znpb-tabs__header {
			justify-content: center;
			padding: 10px;
		}
		.znpb-tabs__header-item {
			padding: 0;
			color: lighten($font-color,15%);
			font-size: 10px;

			&:hover {
				color: lighten($font-color,15%);
				background: transparent;
			}
			&--active {
				color: $surface-active-color;

				&:hover {
					color: darken($surface-headings-color,10%);
				}
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
		color: darken($surface-headings-color,10%);
		cursor: pointer;
	}
}
</style>
