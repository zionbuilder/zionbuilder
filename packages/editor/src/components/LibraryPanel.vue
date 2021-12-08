<template>
	<Loader
		class="znpb-editor-library-modal-loader"
		v-if="loadingLibrary"
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
			<div class="znpb-fancy-scrollbar">
				<CategoriesLibrary
					v-for="(category, index) in computedLibraryCategories"
					:key="index"
					class="znpb-editor-library-modal-sidebar-category"
					:category="category"
					:is-active="category.term_id === activeCategory.term_id"
					:parent="category"
					:subcategory="category.subcategories"
					:active-subcategory="activeSubcategory"
					@activate-category="activeCategory=category, activeSubcategory=null"
					@activate-subcategory="activeCategory=category, activeSubcategory=$event"
				/>
			</div>

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

					<!-- <div
						@click="favActive=!favActive, enteredValue='', $emit('activate-multiple', false)"
						class="znpb-editor-library-modal-subheader__action-title"
						:class="{ 'znpb-editor-library-modal-subheader__action-title--favorite' : favActive }"
					>
						<Icon icon="heart"/>
						{{$translate('favorites')}}
					</div> -->
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
					<li class="znpb-editor-library-modal__item--grid-sizer"></li>
					<li class="znpb-editor-library-modal__item--gutter-sizer"></li>

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
import { getLibraryItems } from '@zb/rest'
import { translate } from '@zb/i18n'
import { uniq } from 'lodash-es'

import CategoriesLibrary from './library-panel/CategoriesLibrary.vue'
import LibraryItem from './library-panel/LibraryItem.vue'
import localSt from 'localstorage-ttl'

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
		multiple: {
			type: Boolean,
			required: false
		}
	},
	setup (props, { emit }) {
		// NormalVars
		const allCategoyConfig = {
			name: translate('all'),
			slug: 'zion-category-all',
			term_id: '3211329987745'
		}

		// Refs
		const searchInput = ref(null)
		const loadingLibrary = ref(true)
		const libraryItems = ref([])
		const libraryCategories = ref([])
		const activeCategory = ref(allCategoyConfig)
		const activeSubcategory = ref(null)
		const sortAscending = ref(false)
		const searchKeyword = ref('')
		const activeItem = ref(null)

		// check if get items from server or from local storage
		getLibraryData()

		// Computed values
		const computedLibraryCategories = computed(() => {
			const categories = []
			let filteredCategories = libraryCategories.value

			// Add the all category
			categories.push(allCategoyConfig)

			if (searchKeyword.value.length > 0) {
				filteredCategories = libraryCategories.value.filter(category => {
					return filteredItemsCategories.value.includes(category.term_id)
				})
			}

			// Add real categories
			filteredCategories.forEach((category) => {
				const subcategories = []
				const categoryConfig = category

				filteredCategories.forEach((subcategory) => {
					if (subcategory.parent && subcategory.parent === category.term_id) {
						subcategories.push(subcategory)
					}
				})

				if (subcategories.length > 0) {
					subcategories.unshift({
						term_id: 'all',
						name: translate('all'),
					})
					categoryConfig.subcategories = subcategories
				}

				categories.push(categoryConfig)
			})

			return categories
		})

		const numberOfElements = computed(() => {
			return `(${filteredItems.value.length})`
		})

		const libraryTitle = computed(() => {
			const breadcrumbs = []

			if (activeCategory.value) {
				breadcrumbs.push(activeCategory.value.name)
			}

			if (activeSubcategory.value) {
				breadcrumbs.push(activeSubcategory.value.name)
			}
			return breadcrumbs.join(' / ')
		})

		const filteredItemsBySearchKeyword = computed(() => {
			let items = libraryItems.value
			// Check for keyword
			if (searchKeyword.value.length > 0) {
				items = libraryItems.value.filter(item => {
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
				items = libraryItems.value
			} else {
				// Get active items by category / subcategory
				items = filteredItemsBySearchKeyword.value.filter(item => {
					// check if a subcategory is active
					if (activeSubcategory.value && activeSubcategory.value.term_id !== 'all') {
						if (item.category.includes(activeCategory.value.term_id) && item.category.includes(activeSubcategory.value.term_id)) {
							return true
						}
					} else {
						if (item.category.includes(activeCategory.value.term_id)) {
							return true
						}
					}

					return false
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
			if (loadingLibrary.value === false) {
				// focus input
				nextTick(() => {
					searchInput.value.focus()
				})
			}
		})

		// Check to see if the current active category is still valid
		watch(searchKeyword, (newValue) => {
			if (newValue.length > 0) {
				const activeCategoryValid = computedLibraryCategories.value.find(category => category.term_id === activeCategory.value.term_id)

				if (!activeCategoryValid) {
					activeCategory.value = allCategoyConfig
					activeSubcategory.value = null
				}
			}
		})

		// Methods
		function getLibraryData () {
			const cachedData = localSt.get('znpbLibraryCache')
			if (cachedData !== null) {
				// Start loading
				loadingLibrary.value = true
				emit('loading-start', true)

				libraryCategories.value = Object.values(cachedData.categories || {})
				libraryItems.value = cachedData.items

				// End loading
				loadingLibrary.value = false
				emit('loading-end', true)
			} else {
				getDataFromServer()
			}
		}

		function getDataFromServer (useCache = true) {
			// Start loading
			loadingLibrary.value = true
			emit('loading-start', true)

			getLibraryItems(useCache).then((response) => {
				const { data = {} } = response
				const { categories = {}, items = [] } = data

				libraryCategories.value = Object.values(categories)
				libraryItems.value = items
				localSt.set('znpbLibraryCache', {
					categories,
					items
				}, 604800000)
			}).finally(() => {
				// End loading
				loadingLibrary.value = false
				emit('loading-end', true)
			})

		}

		return {
			// Normal Values

			// Refs
			searchInput,
			loadingLibrary,
			libraryItems,
			activeCategory,
			activeSubcategory,
			sortAscending,
			searchKeyword,
			activeItem,

			// computed
			computedLibraryCategories,
			numberOfElements,
			filteredItems,
			libraryTitle,

			// Methods
			getDataFromServer
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
