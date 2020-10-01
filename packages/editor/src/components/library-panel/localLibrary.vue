<template>

	<div class="znpb-editor-library-modal">
		<div class="znpb-editor-library-modal-sidebar">
			<CategoriesLibrary
				v-for="(category, index) in allTemplateTypes"
				:key="index"
				class="znpb-editor-library-modal-sidebar-category"
				:category="category"
				:is-expanded="category===getActiveCategory"
				:show-count="false"
				:parent="category"
				:subcategory="getSubCategories"
				:activeSubcategory="activeSubcategory"
				@activate-category="activeCategory=$event, activeSubcategory=getSubCategories[0], $emit('active-upload-finished',true)"
				@activate-subcategory="onSubCategoryChanged"
			/>

		</div>
		<div class="znpb-editor-library-modal-body">
			<div class="znpb-editor-library-modal-subheader">
				<div class="znpb-editor-library-modal-subheader__left">
					<h3 class="znpb-editor-library-modal-subheader__left-title">
						{{getActiveCategory.name}}
					</h3>
				</div>
			</div>

			<Loader v-if="loadingLibrary" />

			<div
				v-else
				class="znpb-editor-library-modal-column-wrapper znpb-fancy-scrollbar"
			>
				<TemplateList
					v-if="getFilteredTemplates.length > 0"
					:templates="getFilteredTemplates"
					:show-insert="true"
					:active-item="activeItem"
					:loading-item="loadingItem"
					@insert="onTemplateInsert"
				/>

				<p
					v-if="(getFilteredTemplates.length < 1)"
					class="znpb-editor-library-modal-no-more"
				>
					{{$translate('no_elements')}}
				</p>

			</div>
		</div>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import { ModalConfirm, Loader } from '@zb/components'
import CategoriesLibrary from './CategoriesLibrary.vue'
import { BaseInput } from '@zb/components/forms'
import TemplateList from '@zionbuilder/admin/src/components/templates/TemplateList.vue'

export default {
	name: 'localLibrary',
	components: {
		CategoriesLibrary,
		TemplateList,
		Loader,
		ModalConfirm
	},
	inject: {
		Library: {
			default () {
				return {}
			}
		}
	},
	props: {
		previewOpen: {
			type: Boolean,
			required: false
		}
	},
	data: function () {
		return {
			showModalConfirm: false,
			activeTemplate: null,
			loadingLibrary: true,
			iframeLoaded: false,
			enteredValue: '',
			searchCategories: [],
			searchElements: [],
			activeSubcategory: null,
			activeCategory: {},
			timeExpired: true,
			ItemLoading: false,
			errorMessage: '',
			allItems: []
		}
	},

	computed: {
		...mapGetters([
			'getTemplates'
		]),
		allTemplateTypes () {
			return window.ZnPbInitalData.template_types
		},
		getActiveCategory () {
			// Don't proceed if we have no categories
			if (Object.keys(this.allTemplateTypes).length === 0) {
				return {}
			}

			// If an active category was not set, get the first category
			if (this.Library['_data'].templateUploaded && this.lastItemImported !== undefined) {
				return this.allTemplateTypes.find(cat => cat.id === this.lastItemImported.template_type) || {}
			}

			if (this.activeCategory && Object.keys(this.activeCategory).length === 0 && this.activeCategory.constructor === Object) {
				return this.allTemplateTypes[0]
			} else {
				return this.activeCategory
			}
		},
		lastItemImported () {
			return this.Library['_data'].templateUploaded ? this.allItems[0] : {}
		},
		getFilteredTemplates () {
			if (this.keyword.length > 1) {
				return this.searchElements
			} else {
				const activeCategory = this.getActiveCategory.id
				const activeSubcategory = this.activeSubcategory ? this.activeSubcategory.slug : false
				return this.allItems.filter((template) => {
					if (activeSubcategory && activeSubcategory !== 'all') {
						// Check to see if the subactegory match
						const templateCategories = template.template_category
						const sameSubcategory = templateCategories.find(category => category.slug === activeSubcategory)
						return template.post_status === 'publish' && template.template_type && template.template_type === activeCategory && sameSubcategory
					} else {
						return template.post_status === 'publish' && template.template_type && template.template_type === activeCategory
					}
				})
			}
		},

		getCategoryCount () {
			return this.getActiveCategory.category_type
		},
		keyword: {
			get () {
				return this.enteredValue
			},
			set (newValue) {
				this.enteredValue = newValue
				if (newValue.length > 1) {
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
		getCategoriesForDisplay () {
			const categories = []

			this.allTemplateTypes.forEach(templateType => {
				// Attach subcategories
				categories.push(templateType)
			})

			return categories
		},

		getSubCategories () {
			// get subcategories from the active category
			let categoryId = this.getActiveCategory.id
			let allCount = 0
			const subcategories = []

			const templateByActiveType = this.getTemplatesByType(categoryId)

			const addedCategories = []

			if (templateByActiveType && templateByActiveType.length > 0) {
				templateByActiveType.forEach(template => {
					if (template.template_category) {
						template.template_category.forEach(category => {
							if (!addedCategories.includes(category.slug)) {
								subcategories.push(category)
								addedCategories.push(category.slug)
							}
						})
					}
				})
			}

			subcategories.unshift({
				id: 'all',
				slug: 'all',
				name: this.$translate('all'),
				count: allCount
			})

			return subcategories
		},
		activeItem () {
			if (this.timeExpired) {
				return null
			}

			return this.lastItemImported.ID
		},
		loadingItem () {
			if (!this.ItemLoading) {
				return null
			}

			return this.activeTemplate
		}
	},
	created () {
		this.getDataFromServer()
	},
	methods: {
		...mapActions([
			'fetchTemplates'
		]),
		getDataFromServer (force = false) {
			this.loadingLibrary = true
			this.$emit('loading-start', true)
			this.fetchTemplates(force).then(() => {
				this.allItems = this.getTemplates

				if (this.Library['_data'].templateUploaded) {
					this.activeCategory = this.allTemplateTypes.find(cat => cat.id === this.allItems[0].template_type)
					this.activeSubcategory = this.getSubCategories[0]
					this.timeExpired = false
					this.setExpireClass()
				}
			}).finally(() => {
				this.loadingLibrary = false
				this.$emit('loading-end', true)
			})
		},

		setExpireClass (startTime) {
			setTimeout(() => {
				this.timeExpired = true
			}, 5000)
		},
		onTemplateInsert (template) {
			this.errorMessage = ''
			this.ItemLoading = true
			this.activeTemplate = template
			this.Library.insertItem(template).then(() => {
				// this.ItemLoading = false
			}).catch((error) => {
				this.errorMessage = error.response.data.message
			})
		},
		getTemplatesByType (type) {
			return this.allItems.filter(template => {
				return template.template_type === type
			})
		},
		searchResult (keyword) {
			let scArray = []

			this.allItems.forEach(function (item, index) {
				// check if item exists in the search array

				if (scArray.find(k => k.id === item.id) === undefined) {
					// check if name includes keyword
					let name = item.post_name.toLowerCase()

					if (name.includes(keyword.toLowerCase())) {
						scArray.push(item)
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
					this.getCategoriesForDisplay.forEach((category, index) => {
						if (item.template_type.slug.includes(category.id)) {
							catArray.push(category)
						}
					})
				}
			})
			return catArray
		},
		onSubCategoryChanged (payload) {
			this.activeSubcategory = payload
			this.$emit('active-upload-finished', true)
		}

	}

}
</script>
<style lang="scss">
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
.slide-preview-enter-to, .slide-preview-leave-from {
	transition: all .2s;
}
</style>
