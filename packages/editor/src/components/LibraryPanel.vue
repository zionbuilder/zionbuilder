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
					v-model="keyword"
					:clearable="true"
					:placeholder="$translate('search_library')"
					ref="searchInput"
				/>
			</div>

			<CategoriesLibrary
				v-for="(category, index) in computedCategories"
				:key="index"
				class="znpb-editor-library-modal-sidebar-category"
				:category="category"
				:is-expanded="category===activeCategory"
				:parent="category"
				:subcategory="getSubCategories"
				:active-subcategory="activeSubcategory"
				@activate-category="activeCategory=$event, activeSubcategory=null"
				@activate-subcategory="activeSubcategory=$event"
			/>

		</div>
		<div class="znpb-editor-library-modal-body">
			<div class="znpb-editor-library-modal-subheader">
				<div class="znpb-editor-library-modal-subheader__left">
					<h3 class="znpb-editor-library-modal-subheader__left-title">
						{{libraryTitle}}
					</h3>
					<span class="znpb-editor-library-modal-subheader__left-number">
						{{getNumberOfElements()}}
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
						@click="sort=!sort"
						class="znpb-editor-library-modal-subheader__action-title"
					>
						<Icon icon="reverse-y" />{{$translate('sort')}}
					</div>

				</div>
			</div>
			<div class="znpb-editor-library-modal-column-wrapper znpb-fancy-scrollbar">

				<ul
					ref="gridlist"
					class="znpb-editor-library-modal-item-list"
				>
					<li class="znpb-editor-library-modal__item--grid-sizer"></li>
					<li class="znpb-editor-library-modal__item--gutter-sizer"></li>

					<LibraryItem
						v-for="(item, index) in filteredItems"
						:key="index"
						:item="item"
						@added-to-favorite="addFavorite(item)"
						@activate-item="checkActiveItem($event)"
						:favorite="checkFavorite(item)"
					/>

				</ul>

				<p
					v-if="(filteredItems.length < 4)"
					class="znpb-editor-library-modal-no-more"
				>
					{{$translate('no_more_to_show')}}
				</p>

			</div>

		</div>
		<transition name="slide-preview">
			<div
				v-if="previewOpen && activeItem.type==='single'"
				class="znpb-editor-library-modal-preview znpb-fancy-scrollbar"
			>
				<iframe
					id="znpb-editor-library-modal-preview-iframe"
					frameborder="0"
					:src="activeItem.preview_url"
					@load="iframeLoaded = true"
				>
				</iframe>
			</div>
		</transition>
	</div>
</template>

<script>
// import { getLibraryItems } from '@zb/rest'
import CategoriesLibrary from './library-panel/CategoriesLibrary.vue'
import LibraryItem from './library-panel/CategoriesLibrary.vue'
import localSt from 'localstorage-ttl'
import { onBeforeUnmount, computed, provide, inject, ref, reactive, onMounted, nextTick, watch, toRefs } from 'vue'
import { translate } from '@zb/i18n'
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
	watch: {
		keyword (newValue) {
			// // Check to see if the active category is still relevant
			// let foundCategory = false
			// let foundSubcategory = false
			// for (let categoryId in this.computedCategories) {
			// 	const category = this.computedCategories[categoryId]
			// 	if (this.activeCategory && this.activeCategory.term_id === category.term_id) {
			// 		foundCategory = true
			// 	}
			// 	if (this.activeSubcategory && this.activeSubcategory.term_id === category.term_id) {
			// 		foundSubcategory = true
			// 	}
			// }
			// if (!foundCategory) {
			// 	this.activeCategory = null
			// 	this.activeSubcategory = null
			// } else if (!foundSubcategory) {
			// 	this.activeSubcategory = null
			// }
		},
		previewOpen (newVal) {

			if (newVal === false && this.multiple) {
				this.items.value.forEach(function (item, index) {
					if (item.id === this.activeItem.value.parent) {
						activeItem.value = item
					}
				})
			}
		},
		filteredItems (newVal) {

			if (newVal) {
				// nextTick(() => {
				this.onLayout()
				// })
			}
		},
		loadingLibrary (loadingLibrary, prevloadingLibrary) {
			if (!loadingLibrary) {
				// start masonry
				this.initMasonry()

				// focus input
				// nextTick(() => searchInput.value.focus())
			}
		}
	},

	setup (props, { emit }) {
		// const gridlist = ref()
		const searchInput = ref()
		// const msnry = ref(null)
		const loadingLibrary = ref(true)
		const iframeLoaded = ref(true)
		const enteredValue = ref('')
		const searchCategories = ref([])
		const searchElements = ref([])
		const computedItems = ref(false)
		const favActive = ref(false)
		const sort = ref(false)
		const activeItem = ref(null)
		const activeCategory = ref(null)
		const activeSubcategory = ref(null)
		const allCategory = ref({ term_id: 'all', name: 'All' })
		const favorites = ref([])
		const categories = ref({})
		const items = ref([])

		const cachedData = localSt.get('znpbLibraryCache')
		// check if get items from server or from local storage

		if (cachedData !== null) {
			categories.value = cachedData.categories
			items.value = cachedData.items
		}


		const computedCategories = computed(() => {
			if ((keyword.value.length > 1) && (searchCategories.value.length > 0)) {
				return searchCategories.value
			} else {
				return categories.value
			}
		})


		onMounted(() => {
			if (cachedData !== null) {
				loadingLibrary.value = false
			}
		})


		const libraryTitle = computed(() => {
			if (favActive.value) {
				return translate('my_favorites')
			} else if (props.multiple === false) {
				if (activeSubcategory.value) {
					return activeSubcategory.value.name
				} else {
					return getActiveCategory.value.name
				}
			} else if (activeItem.value) {
				return `${activeItem.value.name} ${translate('pages')}`
			}

			return ''
		})

		const keyword = computed({
			get: () => {
				return enteredValue.value
			},
			set: newValue => {
				enteredValue = newValue
				if (newValue.length > 1) {
					favActive.value = false
					const searchResultValue = searchResult(newValue)

					if (searchResultValue) {
						searchElements.value = searchResultValue
						searchCategories.value = searchResultCategories()
					} else searchCategories.value = []
				} else if (keyword.value.length === 0) {
					searchCategories.value = []
				}
			}
		})



		const getActiveCategory = computed(() => {
			// Don't proceed if we have no categories
			if (Object.keys(computedCategories.value).length === 0) {
				return {}
			}

			// If an active category was not set, get the first category
			if (activeCategory.value === null) {
				return computedCategories.value[Object.keys(computedCategories.value)[0]]
			} else {
				return activeCategory.value
			}
		})

		const getCategoryIdByIndex = computed(() => {
			return getActiveCategory.value.term_id
		})

		const filteredItems = computed(() => {
			let filteredarray = []
			if (props.multiple || (props.multiple && favActive.value)) {
				let multipleArray = []
				items.value.forEach(function (item, index) {
					if (item.parent === activeItem.value.id) {
						multipleArray.push(item)
					}
				})
				filteredarray = multipleArray
			} else if (favActive.value) {
				filteredarray = favorites.value
			} else {
				filteredarray = getSubcategoryItems.value
			}

			// Create a clone for reverse since the reverse is in place
			if (sort.value) {
				filteredarray = [...filteredarray].reverse()
			}
			return filteredarray
		})

		const getSubcategoryItems = computed(() => {
			let newArrayItems = []
			let itemsArray = []

			if (keyword.value.length > 1) {
				itemsArray = searchElements.value
			} else {
				itemsArray = items.value
			}
			// iterate through itemsArray to check for subcategory
			itemsArray.forEach((item, index) => {
				// check if a subcategory is active
				if (activeSubcategory.value && activeSubcategory.value.term_id !== 'all') {
					if (item.category.includes(getCategoryIdByIndex.value) && item.category.includes(activeSubcategory.value.term_id)) {
						newArrayItems.push(item)
					}
				} else {
					if (item.category.includes(getCategoryIdByIndex.value)) {
						newArrayItems.push(item)
					}
				}
			})

			return newArrayItems
		})

		const getSubCategories = computed(() => {
			// get subcategories from the active category
			let categoryId = getCategoryIdByIndex.value
			let allCount = 0
			const subcategories = []

			if (typeof computedCategories.value === 'object') {
				Object.keys(computedCategories.value).forEach((categoryIndex) => {
					const category = computedCategories.value[categoryIndex]
					if (category.parent && category.parent === categoryId) {
						subcategories.push(category)
						allCount += category.count
					}
				})
			}

			subcategories.unshift({
				term_id: 'all',
				name: translate('all'),
				count: allCount
			})

			return subcategories
		})

		function searchResult (keyword) {
			let scArray = []

			// search after elements
			items.value.forEach(function (item, index) {
				// check if item exists in the search array

				if (scArray.find(k => k.id === item.id) === undefined) {
					// check if name includes keyword
					let name = item.name.toLowerCase()

					if (name.includes(keyword.toLowerCase())) {
						scArray.push(item)
					} else {
						// check if tags include keywords
						item.tags.forEach(function (tag, index) {
							if (tag.includes(keyword.toLowerCase())) {
								scArray.push(item)
							}
						})
					}
				}
			})

			return scArray
		}



		// function onLayout () {
		// 	// destroy old masonry
		// 	if (msnry.value.length) {
		// 		msnry.destroy()
		// 	}
		// 	// create new masonry
		// 	initMasonry()
		// }

		function searchResultCategories () {
			let catArray = []

			// show only categories that have the elements matching keyword
			searchElements.value.forEach((item, index) => {
				let categExists = false

				catArray.forEach(function (cat, catindex) {
					if (cat.hasOwnProperty(item.category)) {
						categExists = true
					}
				})

				if (!categExists) {
					for (let category in categories.value) {
						if (categories.value.hasOwnProperty(category)) {
							if (item.category.includes(category.term_id)) {
								catArray.push(category)
							}
						}
					}
				}
			})
			return catArray
		}

		const getNumberOfElements = () => {
			return `(${filteredItems.value.length})`
		}

		function checkActiveItem (item) {
			activeItem.value = item
			if (item.type === 'single') {
				emit('activate-preview', activeItem.value)
			} else {
				emit('activate-multiple', true)
			}
		}

		function checkFavorite (item) {
			let favorite = false
			favorites.value.forEach(function (favItem, index) {
				if (favItem.id === item.id) {
					favorite = true
				}
			})
			return favorite
		}

		function addFavorite (item) {
			let foundIndex = -1
			favorites.value.forEach(function (favItem, index) {
				if (favItem.id === item.id) {
					foundIndex = index
				}
			})
			if (foundIndex === -1) {
				favorites.value.push(item)
			} else {
				favorites.value.splice(foundIndex, 1)
			}
		}

		function getCategoryTitle (category) {
			// get the category id === key
			let categoryId = Object.keys(category)
			let catObejct = null
			for (let categoryId in computedCategories.value) {
				if (computedCategories.hasOwnProperty(category)) {
					catObejct = category[categoryId]
				}
			}
			// this.computedCategories.forEach(function (cat, catindex) {
			// 	if (cat.hasOwnProperty(categoryId)) {
			// 		catObejct = cat[categoryId]
			// 	}
			// })
			return catObejct.title
		}

		// onBeforeUnmount(() => {
		// 	if (msnry) {
		// 		msnry.value.destroy()
		// 	}
		// })

		return {
			// gridlist,
			// initMasonry,
			// msnry,
			loadingLibrary,
			iframeLoaded,
			enteredValue,
			searchCategories,
			searchElements,
			computedItems,
			sort,
			activeItem,
			activeCategory,
			activeSubcategory,
			allCategory,
			favorites,
			categories,
			items,
			libraryTitle,
			keyword,
			computedCategories,
			getActiveCategory,
			getCategoryIdByIndex,
			filteredItems,
			getSubCategories,
			getNumberOfElements,
			checkActiveItem,
			checkFavorite,
			addFavorite,
			getCategoryTitle,
			searchInput
		}
	},
	methods: {
		onLayout () {
			// destroy old masonry
			if (this.msnry.length) {
				this.msnry.destroy()
			}
			// create new masonry
			this.initMasonry()
		},
		initMasonry () {
			nextTick(() => {
				let grid = document.querySelectorAll('.znpb-editor-library-modal-item-list')

				window.jQuery(grid[0]).imagesLoaded(() => {
					this.msnry = new window.Masonry(grid[0], {
						columnWidth: '.znpb-editor-library-modal__item--grid-sizer',
						itemSelector: '.znpb-editor-library-modal__item',
						gutter: '.znpb-editor-library-modal__item--gutter-sizer',
						transitionDuration: 0
					})
				})
			})
		}
	},
	beforeUnmount () {
		if (this.msnry) {
			this.msnry.destroy()
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
		padding: 20px;

		&__right, &__left {
			display: flex;
			align-items: center;
		}

		&__left-title {
			margin-right: 8px;
			color: $surface-active-color;
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
		height: 100%;
		height: 100%;
		min-height: 300px;
		padding: 0 20px 80px 20px;
		transform: translateY(80px);

		p.znpb-editor-library-modal-no-more {
			margin-bottom: 40px;
			text-align: center;
		}
		& > .znpb-editor-library-modal-item-list {
			margin: 0 auto;
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
				fill: $secondary;
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
	border-right: 1px solid $surface-variant;
	& > div {
		border-bottom: 1px solid $surface-variant;
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
	background: #fff;
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
