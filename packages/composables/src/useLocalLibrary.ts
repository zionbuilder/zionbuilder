import { ref } from 'vue'
import {
	getTemplates,
	addTemplate as addTemplateToDB,
	deleteTemplate as deleteTemplateToDB,
	importTemplateLibrary
} from '@zionbuilder/rest'
import { remove } from 'lodash-es'

const libaryItems = ref([])
const loading = ref(false)
let fetched = false

export const useLocalLibrary = () => {
	function fetchTemplates(force = false) {
		if (fetched && !force) {
			return
		}

		loading.value = true

		return getTemplates().then((response) => {
			libaryItems.value = response.data
			// Allow chaining
			return Promise.resolve(response)
		}).finally(() => {
			loading.value = false
			fetched = true
		})
	}

	function addTemplate(template) {
		loading.value = true

		return addTemplateToDB(template).then((response) => {
			libaryItems.value.push(response.data)

			// Allow chaining
			return Promise.resolve(response)
		}).finally(() => {
			loading.value = false
		})
	}

	function importTemplate(formData) {
		loading.value = true

		return importTemplateLibrary(formData).then((result) => {
			addTemplate(result.data)
			return Promise.resolve(result)
		})
			.finally(() => {
				loading.value = false
			})
	}

	function deleteTemplate(templateID) {
		loading.value = true

		return deleteTemplateToDB(templateID).then((response) => {
			remove(libaryItems.value, (template) => template.ID === templateID)

			// Allow chaining
			return Promise.resolve(response)
		}).finally(() => {
			loading.value = false
		})
	}

	return {
		libaryItems,
		loading,
		fetchTemplates,
		addTemplate,
		deleteTemplate,
		importTemplate
	}
}