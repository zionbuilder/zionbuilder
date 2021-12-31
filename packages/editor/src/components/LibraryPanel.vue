<template>
	<Loader
		class="znpb-editor-library-modal-loader"
		v-if="libraryConfig.loading"
	/>
	<div
		v-else
		class="znpb-editor-library-modal"
	>
		<div class="znpb-editor-library-modal-sidebar">
			<div class="znpb-editor-library-modal-sidebar-search">
				<BaseInput
					icon="search"
					v-model="searchKeyword"
					:clearable="true"
					:placeholder="$translate('search_library')"
					ref="searchInput"
				/>
			</div>

			<CategoriesLibrary
				:categories="computedLibraryCategories"
				:on-category-activate="onCategoryActivate"
			/>

		</div>
		<div class="znpb-editor-library-modal-body">
			<div class="znpb-editor-library-modal-subheader">
				<div class="znpb-editor-library-modal-subheader__left">
					<h3 class="znpb-editor-library-modal-subheader__left-title">
						{{libraryTitle}}
					</h3>
					<span class="znpb-editor-library-modal-subheader__left-number">
						{{numberOfElements}}
					</span>

				</div>
				<div class="znpb-editor-library-modal-subheader__right">
					<div
						@click="sortAscending=!sortAscending"
						class="znpb-editor-library-modal-subheader__action-title"
					>
						<Icon icon="reverse-y" />{{$translate('sort')}}
					</div>

				</div>
			</div>
			<div class="znpb-editor-library-modal-column-wrapper znpb-fancy-scrollbar">
				<ul class="znpb-editor-library-modal-item-list">
					<LibraryItem
						v-for="item in filteredItems"
						:key="item.id"
						:item="item"
						@activate-item="$emit('activate-preview', item), activeItem = item"
					/>
				</ul>

				<p
					v-if="searchKeyword.length > 0 && filteredItems.length === 0"
					class="znpb-editor-library-modal-no-more"
				>
					{{$translate('no_more_to_show')}}
				</p>

			</div>

		</div>

		<transition name="slide-preview">
			<div
				v-if="previewOpen && activeItem"
				class="znpb-editor-library-modal-preview znpb-fancy-scrollbar"
			>
				<iframe
					id="znpb-editor-library-modal-preview-iframe"
					frameborder="0"
					:src="activeItem.preview_url"
				>
				</iframe>
			</div>
		</transition>
	</div>
</template>

<script>
import { ref, computed, watchEffect, watch, nextTick } from 'vue'
import { translate } from '@zb/i18n'
import { uniq } from 'lodash-es'

import CategoriesLibrary from './library-panel/CategoriesLibrary.vue'
import LibraryItem from './library-panel/LibraryItem.vue'

export default {
	name: 'LibraryPanel',
	components: {
		CategoriesLibrary,
		LibraryItem
	},
	props: {
		previewOpen: {
			type: Boolean,
			required: false
		},
		libraryConfig: {
			type: Object,
			required: true
		}
	},
	setup (props, { emit }) {
		// NormalVars
		const allCategoyConfig = {
			name: translate('all'),
			slug: 'zion-category-all',
			term_id: '3211329987745',
			isActive: true
		}
		console.log(props.libraryConfig);
		// Refs
		const searchInput = ref(null)
		const libraryItems = props.libraryConfig.items
		const libraryCategories = props.libraryConfig.categories
		const activeCategory = ref(allCategoyConfig)
		const sortAscending = ref(false)
		const searchKeyword = ref('')
		const activeItem = ref(null)

		// Computed values
		/**
		 * Returns a computed list of all categories, including the 'all' category
		 */
		const computedAllCategories = computed(() => {
			const categories = []

			// Add the all category
			categories.push(allCategoyConfig)

			categories.push(...libraryCategories)

			return categories
		})


		const computedLibraryCategories = computed(() => {
			const categories = []
			let filteredCategories = computedAllCategories.value

			if (searchKeyword.value.length > 0) {
				filteredCategories = computedAllCategories.value.filter(category => {
					return category.term_id === allCategoyConfig.term_id || filteredItemsCategories.value.includes(category.term_id)
				})
			}

			// Add real categories
			filteredCategories.forEach((category) => {
				if (!category.parent) {
					categories.push(createNestedCategories(category, filteredCategories))
				}
			})

			return categories
		})

		function createNestedCategories (categoryConfig, allCategories) {
			const subcategories = []

			allCategories.forEach((subcategory) => {
				if (subcategory.parent && subcategory.parent === categoryConfig.term_id) {
					subcategories.push(createNestedCategories(subcategory, allCategories))
				}
			})

			if (subcategories.length > 0) {
				categoryConfig.subcategories = subcategories
			}

			return categoryConfig
		}

		const numberOfElements = computed(() => {
			return `(${filteredItems.value.length})`
		})

		const libraryTitle = computed(() => {
			return activeCategory.value.name
		})

		const filteredItemsBySearchKeyword = computed(() => {
			let items = libraryItems
			// Check for keyword
			if (searchKeyword.value.length > 0) {
				items = libraryItems.filter(item => {
					// check if name includes keyword
					let name = item.name.toLowerCase()

					if (name.includes(searchKeyword.value.toLowerCase())) {
						return true
					} else {
						// check if tags include keywords
						item.tags.forEach(function (tag, index) {
							if (tag.includes(searchKeyword.value.toLowerCase())) {
								return true
							}
						})
					}

					return false
				})
			}

			return items
		})

		const filteredItems = computed(() => {
			let items = []

			if (activeCategory.value.term_id === allCategoyConfig.term_id) {
				items = libraryItems
			} else {
				// Get active items by category / subcategory
				items = filteredItemsBySearchKeyword.value.filter(item => {
					return item.category.includes(activeCategory.value.term_id)
				})
			}

			// Create a clone for reverse since the reverse is in place
			if (sortAscending.value) {
				items = [...items].reverse()
			}

			return items
		})

		const filteredItemsCategories = computed(() => {
			const activeCategories = []
			filteredItemsBySearchKeyword.value.forEach(item => {
				activeCategories.push(...item.category)
			})

			return uniq(activeCategories)
		})

		// Watchers
		watchEffect(() => {
			if (props.libraryConfig.loading === false) {
				// focus input
				nextTick(() => {
					searchInput.value.focus()
				})
			}
		})

		// Check to see if the current active category is still valid
		watch(searchKeyword, (newValue) => {
			if (newValue.length > 0) {
				const activeCategoryValid = computedlibraryCategories.find(category => category.term_id === activeCategory.value.term_id)

				if (!activeCategoryValid) {
					onCategoryActivate(allCategoyConfig)
				}
			}
		})

		// Methods
		function onCategoryActivate (category) {
			// Deselect active categories
			computedAllCategories.value.forEach(item => item.isActive = false)

			category.isActive = true

			// Activate category parents
			let currentCategory = category
			while (currentCategory && currentCategory.parent) {
				const parentCategory = computedAllCategories.value.find(category => category.term_id === currentCategory.parent)
				if (parentCategory) {
					parentCategory.isActive = true
					currentCategory = parentCategory
				}
			}

			activeCategory.value = category
		}

		return {
			// Refs
			searchInput,
			libraryItems,
			activeCategory,
			sortAscending,
			searchKeyword,
			activeItem,

			// computed
			computedLibraryCategories,
			numberOfElements,
			filteredItems,
			libraryTitle,

			// Methods
			onCategoryActivate
		}
	}
}
</script>
<style lang="scss">
.znpb-column-wrapper {
	max-width: 1180px;
}
.znpb-editor-library-modal {
	display: flex;
	flex-grow: 1;
	max-height: 100%;

	&-subheader {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 20px 20px 0;

		&__right, &__left {
			display: flex;
			align-items: center;
		}

		&__left-title {
			margin-right: 8px;
			color: var(--zb-surface-text-active-color);
			font-size: 18px;
			font-weight: 500;
			text-transform: capitalize;
		}

		&__left-number {
			margin-left: 5px;
			font-size: 16px;
			font-weight: 400;
		}
	}

	&-body {
		position: relative;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
		width: 100%;
		height: 100%;
	}

	&-column-wrapper {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		overflow-x: hidden;
		width: 100%;
		height: calc(100% - 50px);
		min-height: 300px;
		padding: 0 20px 50px 20px;
		transform: translateY(50px);

		p.znpb-editor-library-modal-no-more {
			margin-bottom: 40px;
			text-align: center;
		}
		& > .znpb-editor-library-modal-item-list {
			margin: 0 -10px;
		}
	}
}

.znpb-editor-library-modal-subheader__action-title {
	display: flex;
	margin-right: 25px;
	font-weight: 500;
	cursor: pointer;

	&--favorite {
		.znpb-editor-icon.zion-heart {
			path, path:first-child {
				fill: var(--zb-secondary-color);
			}
		}
	}

	.znpb-editor-icon-wrapper {
		margin-right: 5px;
		font-size: 13px;
	}

	&:last-child {
		margin-right: 0;
	}
}
.znpb-editor-library-modal-sidebar {
	display: flex;
	flex-direction: column;
	flex: 1 0 260px;
	width: 100%;
	max-width: 260px;
	border-right: 1px solid var(--zb-surface-border-color);
	& > div {
		border-bottom: 1px solid var(--zb-surface-border-color);
	}
	&-search {
		padding: 20px;
	}

	 > .znpb-editor-library-modal-category-list {
		padding-top: 8px;
	}
}
#znpb-editor-library-modal-preview-iframe {
	width: 100%;
	height: 100%;
}
.znpb-editor-library-modal-preview {
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	height: 100%;
	background: var(--zb-surface-color);
}
.slide-preview-enter-from {
	opacity: .2;
}
.slide-preview-enter-to {
	opacity: 1;
}
.slide-preview-leave-from {
	opacity: .4;
}
.slide-preview-leave-to {
	opacity: 0;
}
.slide-preview-enter-to, .slide-preview-leave-to {
	transition: all .2s;
}
</style>
