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
						<BaseIcon icon="heart"/>
						{{$translate('favorites')}}
					</div> -->
					<div
						@click="sort=!sort"
						class="znpb-editor-library-modal-subheader__action-title"
					>
						<BaseIcon icon="reverse-y" />{{$translate('sort')}}
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
import { getLibraryItems } from '@/api/Library'
import CategoriesLibrary from '@/editor/components/library-panel/CategoriesLibrary.vue'
import LibraryItem from '@/editor/components/library-panel/LibraryItem.vue'
import { BaseInput } from '@zb/components/forms'

import localSt from 'localstorage-ttl'
export default {
	name: 'LibraryPanel',
	components: {
		CategoriesLibrary,
		LibraryItem,
		BaseInput

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
			let self = this
			if (newVal === false && this.multiple) {
				this.items.forEach(function (item, index) {
					if (item.id === self.activeItem.parent) {
						self.activeItem = item
					}
				})
			}
		},
		filteredItems (newVal) {
			if (newVal) {
				this.$nextTick(() => {
					this.onLayout()
				})
			}
		},
		multiple (newVal) {
			if (newVal === true) {
				this.enteredValue = ''
			}
		},
		loadingLibrary (newVal) {
			if (!newVal) {
				// start masonry
				this.initMasonry()

				// focus input
				this.$nextTick(() => this.$refs.searchInput.focus())
			}
		}
	},
	data: function () {
		return {
			loadingLibrary: true,
			iframeLoaded: false,
			enteredValue: '',
			searchCategories: [],
			searchElements: [],
			computedItems: false,
			favActive: false,
			sort: false,
			activeItem: null,
			activeCategory: null,
			activeSubcategory: null,
			allCategory: {
				term_id: 'all',
				name: 'All'
			},
			favorites: [],
			categories: {},
			items: []
		}
	},
	computed: {
		libraryTitle () {
			if (this.favActive) {
				return this.$translate('my_favorites')
			} else if (this.multiple === false) {
				if (this.activeSubcategory) {
					return this.activeSubcategory.name
				} else {
					return this.getActiveCategory.name
				}
			} else if (this.activeItem) {
				return `${this.activeItem.name} ${this.$translate('pages')}`
			}

			return ''
		},
		keyword: {
			get () {
				return this.enteredValue
			},
			set (newValue) {
				this.enteredValue = newValue
				if (newValue.length > 1) {
					this.favActive = false
					const searchResult = this.searchResult(newValue)

					if (searchResult) {
						this.searchElements = searchResult
						this.searchCategories = this.searchResultCategories()
					} else this.searchCategories = []
				} else if (this.keyword.length === 0) {
					this.searchCategories = []
				}
			}
		},
		computedCategories () {
			if ((this.keyword.length > 1) && (this.searchCategories.length > 0)) {
				return this.searchCategories
			} else {
				return this.categories
			}
		},
		getActiveCategory () {
			// Don't proceed if we have no categories
			if (Object.keys(this.computedCategories).length === 0) {
				return {}
			}

			// If an active category was not set, get the first category
			if (!this.activeCategory) {
				return this.computedCategories[Object.keys(this.computedCategories)[0]]
			} else {
				return this.activeCategory
			}
		},
		getCategoryIdByIndex () {
			return this.getActiveCategory.term_id
		},
		filteredItems () {
			let self = this
			let a = []
			if (this.multiple || (this.multiple && this.favActive)) {
				let multipleArray = []
				this.items.forEach(function (item, index) {
					if (item.parent === self.activeItem.id) {
						multipleArray.push(item)
					}
				})

				a = multipleArray
			} else if (this.favActive) {
				a = this.favorites
			} else {
				a = this.getSubcategoryItems
			}

			// Create a clone for reverse since the reverse is in place
			if (this.sort) {
				a = [...a].reverse()
			}

			return a
		},
		getSubcategoryItems () {
			let newArrayItems = []
			let itemsArray = []

			if (this.keyword.length > 1) {
				itemsArray = this.searchElements
			} else {
				itemsArray = this.items
			}
			// iterate through itemsArray to check for subcategory
			itemsArray.forEach((item, index) => {
				// check if a subcategory is active
				if (this.activeSubcategory && this.activeSubcategory.term_id !== 'all') {
					if (item.category.includes(this.getCategoryIdByIndex) && item.category.includes(this.activeSubcategory.term_id)) {
						newArrayItems.push(item)
					}
				} else {
					if (item.category.includes(this.getCategoryIdByIndex)) {
						newArrayItems.push(item)
					}
				}
			})

			return newArrayItems
		},

		getSubCategories () {
			// get subcategories from the active category
			let categoryId = this.getCategoryIdByIndex
			let allCount = 0
			const subcategories = []

			if (typeof this.computedCategories === 'object') {
				Object.keys(this.computedCategories).forEach((categoryIndex) => {
					const category = this.computedCategories[categoryIndex]
					if (category.parent && category.parent === categoryId) {
						subcategories.push(category)
						allCount += category.count
					}
				})
			}

			subcategories.unshift({
				term_id: 'all',
				name: this.$translate('all'),
				count: allCount
			})

			return subcategories
		}
	},
	mounted () {
		const cachedData = localSt.get('znpbLibraryCache')
		// check if get items from server or from local storage
		if (cachedData === null) {
			this.getDataFromServer()
		} else {
			this.categories = cachedData.categories
			this.items = cachedData.items
			this.loadingLibrary = false
		}
	},
	methods: {
		initMasonry () {
			this.$nextTick(() => {
				window.jQuery(this.$refs.gridlist).imagesLoaded(() => {
					this.msnry = new window.Masonry(this.$refs.gridlist, {
						columnWidth: '.znpb-editor-library-modal__item--grid-sizer',
						itemSelector: '.znpb-editor-library-modal__item',
						gutter: '.znpb-editor-library-modal__item--gutter-sizer',
						transitionDuration: 0

					})
				})
			})
		},
		getDataFromServer (useCache = true) {
			this.loadingLibrary = true
			this.$emit('loading-start', true)
			getLibraryItems(useCache).then((response) => {
				const { data = {} } = response
				const { categories = {}, items = [] } = data
				this.categories = categories
				this.items = items
				localSt.set('znpbLibraryCache', {
					categories,
					items
				}, 604800000)
			}).finally(() => {
				this.loadingLibrary = false
				this.$emit('loading-end', true)
			})
		},
		onLayout () {
			// destroy old masonry
			if (this.msnry) {
				this.msnry.destroy()
			}

			// create new masonry
			this.initMasonry()
		},

		searchResult (keyword) {
			let scArray = []

			// search after elements
			this.items.forEach(function (item, index) {
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
		},
		searchResultCategories () {
			let catArray = []

			// show only categories that have the elements matching keyword
			this.searchElements.forEach((item, index) => {
				let categExists = false

				catArray.forEach(function (cat, catindex) {
					if (cat.hasOwnProperty(item.category)) {
						categExists = true
					}
				})

				if (!categExists) {
					for (let category in this.categories) {
						if (this.categories.hasOwnProperty(category)) {
							if (item.category.includes(category.term_id)) {
								catArray.push(category)
							}
						}
					}
				}
			})
			return catArray
		},

		getNumberOfElements () {
			return `(${this.filteredItems.length})`
		},

		checkActiveItem (item) {
			this.activeItem = item
			if (item.type === 'single') {
				this.$emit('activate-preview', this.activeItem)
			} else {
				this.$emit('activate-multiple', true)
			}
		},

		checkFavorite (item) {
			let favorite = false
			this.favorites.forEach(function (favItem, index) {
				if (favItem.id === item.id) {
					favorite = true
				}
			})
			return favorite
		},

		addFavorite (item) {
			let self = this
			let foundIndex = -1
			this.favorites.forEach(function (favItem, index) {
				if (favItem.id === item.id) {
					foundIndex = index
				}
			})
			if (foundIndex === -1) {
				self.favorites.push(item)
			} else {
				self.favorites.splice(foundIndex, 1)
			}
		},

		getCategoryTitle (category) {
			// get the category id === key
			let categoryId = Object.keys(category)
			let catObejct = null
			for (let categoryId in this.computedCategories) {
				if (this.computedCategories.hasOwnProperty(category)) {
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

	},

	beforeDestroy () {
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
.slide-preview-enter {
	opacity: .2;
}
.slide-preview-after-enter {
	opacity: 1;
}
.slide-preview-before-leave {
	opacity: .4;
}
.slide-preview-leave-to {
	opacity: 0;
}
.slide-preview-enter-active, .slide-preview-leave-active {
	transition: all .2s;
}
</style>
