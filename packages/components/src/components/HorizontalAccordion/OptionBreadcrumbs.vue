<template>
	<div class="znpb-options-breadcrumbs">
		<Icon
			v-if="showBackButton"
			class="znpb-back-icon-breadcrumbs"
			icon="select"
			@click="onItemClicked(previousItem)"
		/>

		<div
			v-for="(breadcrumb, i) in computedBreadcrumbs"
			:key="i"
			class="znpb-options-breadcrumbs-path"
			:class="{['znpb-options-breadcrumbs-path--current']: i === computedBreadcrumbs.length-1}"
			@click="onItemClicked(previousItem)"
		>
			<span v-html="breadcrumb.title"></span>
			<Icon
				v-if="i +1 < computedBreadcrumbs.length"
				icon="select"
				class="znpb-options-breadcrumbs-path-icon"
			/>
		</div>
	</div>
</template>

<script>
import { Icon } from '../Icon'
export default {
	name: 'OptionBreadcrumbs',
	components: {
		Icon
	},
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
		color: var(--zb-surface-icon-color);
		font-size: 14px;
		background-color: var(--zb-surface-lighter-color);
		border-radius: 3px;
		transition: 0.15s all;
		cursor: pointer;
		&:hover {
			color: var(--zb-surface-text-hover-color);
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
		transition: color 0.15s ease-out;

		&:hover:not(&--current) {
			color: var(--zb-surface-text-color);
		}

		&--current {
			color: var(--zb-surface-text-hover-color);
		}

		&__home {
			&:hover {
				color: var(--zb-surface-text-hover-color);
			}
		}

		&-icon {
			transform: rotate(-90deg);
		}

		&.znpb-options-breadcrumbs-path--back {
			&:hover,
			&:focus {
				color: var(--zb-surface-text-hover-color);
				.znpb-editor-icon-wrapper {
					color: var(--zb-surface-icon-color);
				}
				.znpb-options-breadcrumbs-path__current {
					color: var(--zb-surface-icon-color);
				}
			}
		}

		&--back {
			cursor: pointer;
			&:hover {
				.znpb-editor-icon-wrapper {
					color: var(--zb-surface-icon-color);
				}
			}
		}

		.znpb-editor-icon-wrapper {
			width: 10px;
			margin-right: 5px;
			margin-left: 5px;
			color: var(--zb-surface-icon-color);
		}
	}
}
</style>
