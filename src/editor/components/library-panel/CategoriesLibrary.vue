<template>
	<ul class="znpb-editor-library-modal-category-list znpb-fancy-scrollbar">
		<CategoriesLibraryItem
			v-for="category in categories"
			:key="category.term_id"
			:category="category"
			:is-active="category.isActive"
			:on-category-activate="onCategoryActivate"
			@activate-subcategory="activateCategory"
		/>
	</ul>
</template>
<script lang="ts" setup>
import CategoriesLibraryItem from './CategoriesLibraryItem.vue';

const props = defineProps<{
	categories?: LibraryCategory[];
	onCategoryActivate: (category: LibraryCategory) => void;
}>();

function activateCategory(category: LibraryCategory) {
	props.onCategoryActivate(category);
}
</script>
<style lang="scss">
/* vars */

.znpb-editor-library-modal-category {
	display: flex;
	flex-direction: column;
	flex-shrink: 0;
	max-height: 100%;
	padding: 0;
	border-bottom: 1px solid var(--zb-surface-border-color);
	transition: all 0.2s;

	&:last-child {
		border-bottom: none;
	}

	&__header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 12px 20px;
		cursor: pointer;
		.znpb-editor-icon-wrapper {
			color: var(--zb-surface-icon-color);
		}
	}

	&__title {
		font-size: 13px;
		font-weight: 500;
		transition: color 0.15s;
	}

	&__header:hover &__title {
		color: var(--zb-surface-text-hover-color);
	}

	&__list {
		padding-bottom: 15px !important;

		li {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 8px 30px;
			font-family: var(--zb-font-stack);
			font-size: 14px;
			font-weight: 400;
			cursor: pointer;

			h5 {
				font-family: var(--zb-font-stack);
				font-size: 13px;
				font-weight: 500;
				text-transform: capitalize;
			}
		}
	}

	&-list {
		ul {
			padding-left: 20px;
		}
	}

	&-list ul &__header {
		position: relative;

		&::before {
			content: '';
			position: absolute;
			top: 18px;
			left: 5px;
			width: 8px;
			height: 2px;
			background: var(--zb-surface-border-color);
		}
	}

	&__number {
		padding: 4px 9px;
		font-size: 11px;
		background-color: var(--zb-surface-lighter-color);
		border-radius: 3px;
	}
}
</style>
