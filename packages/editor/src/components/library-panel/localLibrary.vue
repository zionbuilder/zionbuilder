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
				@activate-category="activeCategory=$event, activeSubcategory=null"
				@activate-subcategory="activeSubcategory=$event"
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
import CategoriesLibrary from './CategoriesLibrary.vue'
import TemplateList from '../../../../admin/src/components/templates/TemplateList.vue'
import { computed, inject, ref, reactive, watchEffect, provide } from 'vue'
import { translate } from '@zb/i18n'
import { useEditorData } from '@data'
export default {
	name: 'localLibrary',
	components: {
		CategoriesLibrary,
		TemplateList
	},
	props: {
		previewOpen: {
			type: Boolean,
			required: false
		}
	},
	setup (props, { emit }) {
		const $zb = inject('$zb')
		const templateUploaded = inject('templateUploaded')
		const insertItem = inject('insertItem')
		const searchElements = ref([])
		const loadingLibrary = ref(true)
		const activeCategory = ref({})
		const allItems = ref([])
		const activeSubcategory = ref({})
		const timeExpired = ref(false)
		const allTemplateTypes = ref([])
		const enteredValue = ref('')
		const errorMessage = ref('')
		const ItemLoading = ref(false)
		const activeTemplate = ref(null)
		const searchCategories = ref([])

		const getDataFromServer = inject('localgetDataFromServer')
		const localItems = inject('localItems')

		if (localItems.value.length) {
			allItems.value = localItems.value
		} else {
			getDataFromServer().then(() => {
				allItems.value = $zb.templates.models
				loadingLibrary.value = true
				if (templateUploaded.value) {
					activeCategory.value = allTemplateTypes.value.find(cat => cat.id === allItems.value[0].template_type)
					activeSubcategory.value = getSubCategories.value[0]
					timeExpired.value = false
					setExpireClass()
				}
			}).finally(() => {
				loadingLibrary.value = false
				emit('loading-end', true)
			})
		}

		const { template_types } = useEditorData()
		allTemplateTypes.value = template_types


		const getSubCategories = computed(() => {
			// get subcategories from the active category
			let categoryId = getActiveCategory.value.id
			let allCount = 0
			const subcategories = []
			const templateByActiveType = getTemplatesByType(categoryId)
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
				name: translate('all'),
				count: allCount
			})
			return subcategories
		})

		const getActiveCategory = computed(() => {
			// Don't proceed if we have no categories
			if (Object.keys(allTemplateTypes.value).length === 0) {
				return {}
			}

			// If an active category was not set, get the first category
			if (templateUploaded && lastItemImported.value !== undefined) {
				return allTemplateTypes.value.find(cat => cat.id === lastItemImported.value.template_type) || {}
			}

			if (activeCategory.value && Object.keys(activeCategory.value).length === 0) {
				return allTemplateTypes.value[0]
			} else {
				return activeCategory.value
			}
		})

		const lastItemImported = computed(() => {
			return templateUploaded ? allItems.value[0] : {}
		})

		const activeItem = computed(() => {
			if (timeExpired.value) {
				return null
			}

			return lastItemImported.value.ID
		})

		const getCategoriesForDisplay = computed(() => {
			const categories = []
			allTemplateTypes.value.forEach(templateType => {
				// Attach subcategories
				categories.push(templateType)
			})

			return categories
		})

		const getFilteredTemplates = computed(() => {
			if (keyword.value.length > 1) {
				return searchElements.value
			} else {
				const localactiveCategory = getActiveCategory.value.id
				const localactiveSubcategory = activeSubcategory.value ? activeSubcategory.value.slug : false
				return allItems.value.filter((template) => {
					if (localactiveSubcategory && localactiveSubcategory !== 'all') {
						// Check to see if the subcategory match
						const templateCategories = template.template_category
						const sameSubcategory = templateCategories.find(category => category.slug === localactiveSubcategory)
						return template.post_status === 'publish' && template.template_type && template.template_type === localactiveCategory && sameSubcategory
					} else {
						return template.post_status === 'publish' && template.template_type && template.template_type === localactiveCategory
					}
				})
			}
		})

		const loadingItem = computed(() => {
			if (!ItemLoading.value) {
				return null
			}
			return activeTemplate.value
		})

		const keyword = computed({
			get: () => {
				return enteredValue.value
			},
			set: newValue => {
				enteredValue = newValue
				if (newValue.length > 1) {
					const searchResult = searchResult(newValue)

					if (searchResult) {
						searchElements.value = searchResult
						searchCategories.value = searchResultCategories()
					} else searchCategories.value = []
				} else if (keyword.value.length === 0) {
					searchCategories.value = []
				}
			}
		})

		function getTemplatesByType (type) {
			return allItems.value.filter(template => {
				return template.template_type === type
			})
		}

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
					getCategoriesForDisplay.value.forEach((category, index) => {
						if (item.template_type.slug.includes(category.id)) {
							catArray.push(category)
						}
					})
				}
			})
			return catArray
		}

		function setExpireClass (startTime) {
			setTimeout(() => {
				timeExpired.value = true
			}, 5000)
		}

		function searchResult (keyword) {
			let scArray = []

			allItems.value.forEach(function (item, index) {
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
		}

		function onSubCategoryChanged (payload) {
			activeSubcategory.value = payload
			emit('active-upload-finished', true)
		}

		function onTemplateInsert (template) {
			errorMessage.value = ''
			ItemLoading.value = true
			activeTemplate.value = template
			insertItem(template).then(() => {
				ItemLoading.value = false
			}).catch((error) => {
				console.log('error', error)
				errorMessage.value = error
			})
		}

		function onActivateCategory (newCategory) {
			activeCategory.value = newCategory
			activeSubcategory.value = null
			emit('active-upload-finished', true)
		}

		return {
			loadingLibrary,
			getDataFromServer,
			allItems,
			activeCategory,
			activeSubcategory,
			timeExpired,
			getSubCategories,
			getActiveCategory,
			getCategoriesForDisplay,
			searchResultCategories,
			getFilteredTemplates,
			keyword,
			onSubCategoryChanged,
			onTemplateInsert,
			allTemplateTypes,
			lastItemImported,
			activeItem,
			loadingItem,
			getTemplatesByType,
			setExpireClass,
			searchResult,
			onActivateCategory
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
