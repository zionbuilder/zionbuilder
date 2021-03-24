<template>

	<div class="znpb-editor-library-modal">
		<div class="znpb-editor-library-modal-sidebar">
			<CategoriesLibrary
				v-for="(category, index) in allTemplateTypes"
				:key="index"
				class="znpb-editor-library-modal-sidebar-category"
				:category="category"
				:is-expanded="category.id === getActiveCategory.id"
				:show-count="false"
				:parent="category"
				:subcategory="getSubCategories"
				:activeSubcategory="activeSubcategory"
				@activate-category="activeCategory=$event, activeSubcategory=null"
				@activate-subcategory="activeSubcategory=$event"
			/>
		</div>

		<div class="znpb-editor-library-modal-body">
			<div
				class="znpb-editor-library-modal-subheader"
				v-if="getActiveCategory"
			>
				<div class="znpb-editor-library-modal-subheader__left">
					<h3 class="znpb-editor-library-modal-subheader__left-title">
						{{getActiveCategory.name}} {{activeSubcategory ? '> ' + activeSubcategory.name : ''}}
					</h3>
				</div>
			</div>

			<Loader v-if="loading" />

			<div
				v-else
				class="znpb-editor-library-modal-column-wrapper znpb-fancy-scrollbar"
			>
				<TemplateList
					v-if="filteredTemplates.length > 0"
					:templates="filteredTemplates"
					:show-insert="true"
					:active-item="activeItem"
					@insert="onTemplateInsert"
				/>

				<p
					v-if="(filteredTemplates.length < 1)"
					class="znpb-editor-library-modal-no-more"
				>
					{{$translate('no_elements')}}
				</p>

			</div>
		</div>
	</div>
</template>

<script>
import { ref, computed, inject, watch } from 'vue'
import { useLocalLibrary } from '@zionbuilder/composables'
import { get } from 'lodash-es'

// Components
import CategoriesLibrary from './CategoriesLibrary.vue'
import TemplateList from '../../../../admin/src/components/templates/TemplateList.vue'

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
	setup () {
		const {
			fetchTemplates,
			libaryItems,
			loading,
			importedItem,
			resetImportedItem
		} = useLocalLibrary()

		// Load saved templates
		fetchTemplates()

		// Standard vars
		const allTemplateTypes = window.ZnPbInitalData.template_types

		// Inject the library
		const Library = inject('Library')

		// Refs
		const activeTemplate = ref(null)
		const activeCategory = ref({})
		const activeSubcategory = ref(null)
		const timeExpired = ref(true)
		const ItemLoading = ref(false)
		const errorMessage = ref('')

		// Computed
		const filteredTemplates = computed(() => {
			return libaryItems.value.filter(template => {
				const templateCategories = template.template_category
				if (activeSubcategory.value && activeSubcategory.value.slug !== 'all') {
					return templateCategories.find(category => category.slug === activeSubcategory.value.slug)
				}

				return template.post_status === 'publish' && template.template_type === getActiveCategory.value.id
			})
		})

		const activeItem = computed(() => {
			return get(importedItem.value, 'ID', false)
		})

		const getActiveCategory = computed(() => {
			// Don't proceed if we have no categories
			if (Object.keys(allTemplateTypes).length === 0) {
				return {}
			}

			// If an active category was not set, get the first category
			if (Library.$data.templateUploaded && importedItem.value) {
				return allTemplateTypes.find(cat => cat.id === importedItem.value.template_type) || {}
			}

			if (activeCategory.value && Object.keys(activeCategory.value).length === 0 && activeCategory.value) {
				return allTemplateTypes[0]
			} else {
				return activeCategory.value
			}
		})

		function onTemplateInsert (template) {
			errorMessage.value = ''
			ItemLoading.value = true
			activeTemplate.value = template

			Library.insertItem(template).then(() => {
				ItemLoading.value = false
			}).catch((error) => {
				errorMessage.value = error.response.data.message
			})
		}

		return {
			// Computed
			activeItem,
			getActiveCategory,
			ItemLoading,
			timeExpired,
			errorMessage,
			loading,
			libaryItems,
			allTemplateTypes,
			fetchTemplates,
			filteredTemplates,
			Library,
			activeCategory,
			activeSubcategory,
			// Methods
			onTemplateInsert
		}
	},

	computed: {
		getSubCategories () {
			// get subcategories from the active category
			let categoryId = this.getActiveCategory.id
			let allCount = 0

			const subcategories = []

			const templateByActiveType = this.libaryItems.filter(template => {
				return template.template_type === categoryId
			})

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
