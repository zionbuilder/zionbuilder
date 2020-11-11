import { ref } from 'vue'
import {
	getTemplates,
	addTemplate as addTemplateToDB,
	deleteTemplate as deleteTemplateToDB
} from '@zionbuilder/rest'
import { remove } from 'lodash-es'

const libaryItems = ref([])
const loading = ref(false)

export const useLocalLibrary = () => {
	function fetchTemplates () {
		loading.value = true

		return getTemplates().then((response) => {
			libaryItems.value = response.data
		}).finally(() => {
			loading.value = false
		})
	}

	function addTemplate(template) {
		loading.value = true

		return addTemplateToDB(template).then((response) => {
			libaryItems.value.push(response.data)
		}).finally(() => {
			loading.value = false
		})
	}

	function deleteTemplate(templateID) {
		loading.value = true

		return deleteTemplateToDB(templateID).then((response) => {
			remove(libaryItems.value, (template) => template.ID === templateID)
		}).finally(() => {
			loading.value = false
		})
	}

	return {
		libaryItems,
		loading,
		fetchTemplates,
		addTemplate,
		deleteTemplate
	}
}