<template>
	<ul class="znpb-editor-library-modal-category-list znpb-fancy-scrollbar">
		<CategoriesLibraryItem
			v-for="category in categories"
			:key="category.term_id"
			:category="category"
			@activate-subcategory="onActivateSubcategory"
			:is-active="activeCategory === category"
			:on-category-activate="onCategoryActivate"
		/>
	</ul>
</template>
<script>
import { ref, defineAsyncComponent } from 'vue'

export default {
	name: 'CategoriesLibrary',
	components: {
		CategoriesLibraryItem: defineAsyncComponent(() => import('./CategoriesLibraryItem.vue'))
	},
	props: {
		categories: {
			type: Array,
			required: false
		},
		onCategoryActivate: {
			type: Function,
			required: true
		}
	},
	setup (props, { emit }) {
		const activeCategory = ref(null)
		onActivateSubcategory(props.categories[0])

		function onActivateSubcategory (category) {
			activeCategory.value = category
			props.onCategoryActivate(category)
		}

		return {
			activeCategory,

			onActivateSubcategory
		}
	}
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
	transition: all .2s;

	&:last-child {
		border-bottom: none;
	}

	&__header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 16px 20px;
		cursor: pointer;
		.znpb-editor-icon-wrapper {
			color: var(--zb-surface-icon-color);
		}
	}

	&__title {
		font-size: 13px;
		font-weight: 500;
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

	&__number {
		padding: 4px 9px;
		font-size: 11px;
		background-color: var(--zb-surface-lighter-color);
		border-radius: 3px;
	}
}
</style>
