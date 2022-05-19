<template>
	<div class="znpb-element-options__vertical-breadcrumbs-wrapper">
		<div
			class="znpb-element-options__vertical-breadcrumbs-item znpb-element-options__vertical-breadcrumbs-item--first"
			:class="{ 'znpb-element-options__vertical-breadcrumbs-item--active': activeElement === parents.element }"
			@mouseenter.capture="parents.element.highlight"
			@mouseleave="parents.element.unHighlight"
			@click.stop="editElement(parents.element)"
		>
			<span>{{ parents.element.name }}</span>
		</div>
		<template v-if="parents.children.length > 0">
			<BreadcrumbsItem v-for="child in parents.children" :key="child.element.uid" :parents="child" />
		</template>
	</div>
</template>

<script>
import { useUI, useEditElement } from '../../composables';
import BreadcrumbsItem from './BreadcrumbsItem.vue';

export default {
	name: 'Breadcrumbs',
	components: {
		BreadcrumbsItem,
	},
	props: {
		parents: {
			type: Object,
		},
	},
	setup(props) {
		const { openPanel } = useUI();
		const { editElement, element: activeElement } = useEditElement();

		return {
			openPanel,
			editElement,
			activeElement,
		};
	},
};
</script>

<style lang="scss">
.znpb-element-options__vertical-breadcrumbs {
	&-wrapper {
		position: relative;
		display: flex;
		flex-direction: column;
		overflow: hidden;
		box-shadow: none;

		&:after {
			content: '';
			position: absolute;
			top: 21px;
			left: 0;
			width: 1px;
			height: calc(100% - 44px);
			margin-top: 0;
			background: var(--zb-surface-border-color);
		}
	}

	&-item {
		position: relative;
		display: flex;
		flex-direction: column;
		padding-left: 7px;
		margin-left: 5px;
		cursor: pointer;

		& > span {
			display: block;
			margin-bottom: 12px;
			color: var(--zb-surface-text-color);
			font-size: 13px;
			font-weight: 500;
			white-space: nowrap;
			transition: all 0.15s ease-in;

			&:hover {
				color: var(--zb-surface-text-active-color);
			}
		}

		&:before {
			content: '';
			position: absolute;
			top: 7px;
			right: calc(100% - 2px);
			width: 100px;
			height: 1px;
			background: var(--zb-surface-border-color);
		}

		&--active {
			& > span {
				color: var(--zb-surface-text-active-color);
			}
		}

		.znpb-editor-icon-wrapper {
			width: 10px;
			margin-left: 10px;
		}

		&:last-child {
			.znpb-editor-icon-wrapper {
				display: none;
			}
		}
		&--first {
			padding-left: 0;
			margin: 0;
			border: 0;
			& > span {
				margin-left: 7px;
			}
		}

		.znpb-element-options__vertical-breadcrumbs-wrapper--inner {
			padding: 0 7px;
		}
	}
}
.znpb-element-options__vertical-breadcrumbs-wrapper--inner {
	overflow: visible;
	&:after {
		display: none;
	}
}
.znpb-element-options__breadcrumbs
	> .znpb-element-options__vertical-breadcrumbs-wrapper
	> .znpb-element-options__vertical-breadcrumbs-item--first {
	&:before {
		display: none;
	}
	& > span {
		margin-left: 0;
	}
}
.znpb-element-options__vertical-breadcrumbs-item--first ~ .znpb-element-options__vertical-breadcrumbs-item {
	margin-left: 14px;
}
.znpb-element-options__vertical-breadcrumbs-wrapper--inner {
	& > .znpb-element-options__vertical-breadcrumbs-item--first {
		&:before {
			display: block;
		}
	}
}
</style>
