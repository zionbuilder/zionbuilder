<template>

	<div class="znpb-editor-library-modal">
		<div class="znpb-editor-library-modal-sidebar">
			<CategoriesLibrary
				:categories="allCategories"
				:on-category-activate="onCategoryActivate"
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
import { get, find } from 'lodash-es'

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
		const allTemplateTypes = ref(window.ZnPbInitalData.template_types || [])
		console.log({ allTemplateTypes });
		console.log(libaryItems);

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
		const allCategories = computed(() => {
			const categories = allTemplateTypes.value

			allTemplateTypes.value.forEach(templateTypeConfig => {
				const templateTypeItems = libaryItems.value.filter(item => item.template_type === templateTypeConfig.id)

				templateTypeItems.forEach(templateItem => {
					if (Array.isArray(templateItem.template_category)) {
						templateItem.template_category.forEach(category => {
							if (!find(categories, { slug: category.slug })) {
								templateTypeConfig.subcategories = templateTypeConfig.subcategories || []
								templateTypeConfig.subcategories.push(category)
							}
						})
					}
				})
			})

			return categories
		})

		const filteredTemplates = computed(() => {
			return libaryItems.value.filter(template => {
				return template.post_status === 'publish' && template.template_type === activeCategory.value.id
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

		function onCategoryActivate (category) {
			console.log(category);
		}

		return {
			// Computed
			allCategories,
			activeItem,
			getActiveCategory,
			ItemLoading,
			timeExpired,
			errorMessage,
			loading,
			libaryItems,
			fetchTemplates,
			filteredTemplates,
			Library,
			activeCategory,
			activeSubcategory,
			// Methods
			onTemplateInsert,
			onCategoryActivate
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
