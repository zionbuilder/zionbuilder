<template>
	<div class="znpb-options-breadcrumbs">
		<Icon v-if="showBackButton" class="znpb-back-icon-breadcrumbs" icon="select" @click="onItemClicked(previousItem)" />

		<div
			v-for="(breadcrumb, i) in computedBreadcrumbs"
			:key="i"
			class="znpb-options-breadcrumbs-path"
			:class="{ ['znpb-options-breadcrumbs-path--current']: i === computedBreadcrumbs.length - 1 }"
			@click="onItemClicked(previousItem)"
		>
			<span v-html="breadcrumb.title"></span>
			<Icon v-if="i + 1 < computedBreadcrumbs.length" icon="select" class="znpb-options-breadcrumbs-path-icon" />
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'OptionBreadcrumbs',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';

import { Icon } from '../Icon';

type BreadcrumbItem = { title?: string; callback?: () => void };

const props = defineProps<{
	breadcrumbs?: BreadcrumbItem[];
	showBackButton?: boolean;
}>();

const previousItem = computed(() => {
	return props.breadcrumbs?.[props.breadcrumbs.length - 2];
});

const computedBreadcrumbs = computed(() => {
	return props.breadcrumbs?.slice(Math.max(props.breadcrumbs.length - 2, 1));
});

function onItemClicked(breadcrumbItem: BreadcrumbItem) {
	if (breadcrumbItem.callback !== undefined) {
		breadcrumbItem.callback();
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
