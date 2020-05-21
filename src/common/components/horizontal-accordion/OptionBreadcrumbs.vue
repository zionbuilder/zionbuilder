<template>
	<div class="znpb-options-breadcrumbs">
		<BaseIcon
			v-if="showBackButton"
			class="znpb-back-icon-breadcrumbs"
			icon="select"
			@click.native="onItemClicked(previousItem)"
		/>

		<div
			v-for="(breadcrumb, i) in computedBreadcrumbs"
			:key="i"
			class="znpb-options-breadcrumbs-path"
			:class="{['znpb-options-breadcrumbs-path--current']: i === computedBreadcrumbs.length-1}"
			@click="onItemClicked(previousItem)"
		>
			{{breadcrumb.title}}
			<BaseIcon
				icon="select"
				rotate="-90"
				v-if="i +1 < computedBreadcrumbs.length"
			/>
		</div>
	</div>
</template>

<script>

export default {
	name: 'OptionBreadcrumbs',

	props: {
		breadcrumbs: {
			type: Array
		},
		/**
		 * If the breadcrumbs should show the back button
		 */
		showBackButton: {
			type: Boolean,
			required: false
		}
	},
	data () {
		return {

		}
	},
	computed: {
		previousItem () {
			return this.breadcrumbs[this.breadcrumbs.length - 2]
		},
		computedBreadcrumbs () {
			return this.breadcrumbs.slice(Math.max(this.breadcrumbs.length - 2, 1))
		}
	},
	methods: {
		onItemClicked (breadcrumbItem) {
			if (typeof breadcrumbItem.callback !== 'undefined') {
				breadcrumbItem.callback()
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-options-breadcrumbs {
	display: flex;
	align-items: center;
	padding: 20px;
	cursor: pointer;

	.znpb-back-icon-breadcrumbs {
		width: 22px;
		height: 22px;
		margin-right: 10px;
		color: $icons-color;
		font-size: 14px;
		background-color: $surface-variant;
		border-radius: 3px;
		transition: .15s all;
		cursor: pointer;
		&:hover {
			color: darken($icons-color, 5%);
		}

		svg {
			transform: rotate(90deg);
		}
	}
	&-path {
		position: relative;
		top: -1px;
		display: inline-flex;
		align-items: center;
		height: 100%;
		font-size: 13px;
		font-weight: 500;
		transition: color .15s ease-out;

		&:hover:not(&--current) {
			color: lighten($surface-active-color, 30%);
		}

		&--current {
			color: $surface-active-color;
		}

		&__home {
			&:hover {
				color: $surface-active-color;
			}
		}

		&.znpb-options-breadcrumbs-path--back {
			&:hover, &:focus {
				color: $surface-active-color;
				.znpb-editor-icon-wrapper {
					color: $surface-headings-color;
				}
				.znpb-options-breadcrumbs-path__current {
					color: $surface-headings-color;
				}
			}
		}

		&--back {
			cursor: pointer;
			&:hover {
				.znpb-editor-icon-wrapper {
					color: $surface-headings-color;
				}
			}
		}

		.znpb-editor-icon-wrapper {
			width: 10px;
			margin-right: 5px;
			margin-left: 5px;
			color: $icons-color;
		}
	}
}
</style>
