<template>
	<div
		:class="{'znpb-active': isActive}"
		class="znpb-editor-library-modal-category"
		v-if="hasParent"
	>
		<div
			class="znpb-editor-library-modal-category__header"
			@click="selectCategory"
		>
			<h6
				class="znpb-editor-library-modal-category__title"
				v-html="category.name || ''"
			/>

			<Icon
				v-if="hasSubcategories"
				icon="select"
				:rotate="getRotate"
				class="znpb-editor-library-modal-category__header-icon"
			/>
		</div>

		<ul
			v-if="hasSubcategories && expanded"
			class="znpb-editor-library-modal-category__list znpb-fancy-scrollbar"
		>

			<CategoriesLibraryItem
				v-for="(subcat,i) in subcategory"
				:key="i"
				:is-active="subcat.term_id===getActiveSubcategory.term_id"
				:show-count="showCount"
				@activate-subcategory="onSubCategoryActive"
				:subcategory="subcat"
			/>
		</ul>

	</div>
</template>
<script>
import CategoriesLibraryItem from './CategoriesLibraryItem.vue'
export default {
	name: 'CategoriesLibrary',
	components: {
		CategoriesLibraryItem
	},
	props: {
		category: {
			type: Object,
			required: false
		},
		isActive: {
			type: Boolean,
			default: false
		},
		parent: {
			required: false
		},
		subcategory: {
			type: Array,
			required: false
		},
		showCount: {
			type: Boolean,
			required: false
		},
		activeSubcategory: {
			type: Object,
			required: false
		}
	},
	data () {
		return {
			expanded: false
		}
	},
	watch: {
		isActive (newVal) {
			if (this.hasSubcategories) {
				this.expanded = newVal
			}
		}
	},
	computed: {
		hasSubcategories () {
			return this.subcategory && this.subcategory.length > 0
		},
		getRotate () {
			return this.expanded ? '180' : '0'
		},
		getActiveSubcategory () {
			return this.activeSubcategory ? this.activeSubcategory : this.subcategory[0]
		},
		hasParent () {
			return this.category !== undefined ? !this.category.parent : false
		}
	},
	methods: {
		selectCategory () {
			if (!this.isExpanded) {
				this.$emit('activate-category', this.parent)
			}

			// Only expand if we have subcategories
			if (this.hasSubcategories) {
				this.expanded = !this.expanded
			}

		},

		onSubCategoryActive (subcategory) {
			this.$emit('activate-subcategory', subcategory)
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
	transition: all .2s;

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

	&.znpb-active {
		flex-shrink: 1;
		.znpb-editor-library-modal-category__title {
			position: relative;
			width: 100%;
			color: var(--zb-surface-text-active-color);
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
