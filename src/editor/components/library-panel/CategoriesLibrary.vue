<template>
	<div
		ref="currentCateg"
		:class="{'znpb-active': isExpanded}"
		class="znpb-editor-library-modal-category znpb-fancy-scrollbar"
		v-if="!category.parent"
	>
		<div
			class="znpb-editor-library-modal-category__header"
			@click="selectCategory"
		>
			<h6 class="znpb-editor-library-modal-category__title">
				{{ category.name }}
			</h6>
			<BaseIcon
				icon="select"
				:rotate="getRotate"
				class="znpb-editor-library-modal-category__header-icon"
			/>
		</div>

		<ul
			v-if="expanded"
			class="znpb-editor-library-modal-category__list"
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
			required: true
		},
		isExpanded: {
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
			expanded: this.isExpanded
		}
	},
	watch: {
		isExpanded (newVal, oldVal) {
			this.expanded = newVal
		}
	},
	computed: {
		getRotate () {
			return this.expanded ? '180' : '0'
		},
		getActiveSubcategory () {
			return this.activeSubcategory ? this.activeSubcategory : this.subcategory[0]
		}
	},
	methods: {
		selectCategory () {
			if (this.isExpanded) {
				if (this.expanded === false) {
					this.expanded = true
				} else this.expanded = false
			} else {
				this.$emit('activate-category', this.parent)
				this.expanded = true
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
			color: $surface-headings-color;
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
			font-family: $font-stack;
			font-size: 14px;
			font-weight: 400;
			cursor: pointer;
			h5 {
				font-family: $font-stack;
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
			color: $surface-active-color;
		}
	}

	&__number {
		padding: 4px 9px;
		font-size: 11px;
		background-color: $surface-variant;
		border-radius: 3px;
	}
}
</style>
