import { savePage as savePageREST } from '@zb/rest'
import { store } from '../store'
import Cache from '../Cache'
import { ref, Ref } from 'vue'
import { useTemplateParts } from './useTemplateParts'

const isSavePageLoading: Ref<boolean> = ref(false)

export function useSavePage () {
	const save = (status = 'publish') => {
		const { getTemplatePart } = useTemplateParts()
		const contentTemplatePart = getTemplatePart('content')

		if (!contentTemplatePart) {
			console.error('Content template data not found.')
			return
		}

		const pageID = store.getters.getPageId
		const pageData = {
			page_id: pageID,
			template_data: contentTemplatePart.toJSON(),
			// page_settings: getters.getPageSettings,
			// css_classes: getters.getClasses
		}

		// Check if this is a draft
		if (status) {
			pageData.status = status
		}

		if (status !== 'autosave') {
			isSavePageLoading.value = true
		}

		return new Promise((resolve, reject) => {
			savePageREST(pageData).catch(error => {
				Cache.saveItem(pageID, pageContent)
				reject(error)
			}).finally(() => {
				isSavePageLoading.value = false
				resolve()
			})
		})
	}

	const savePage = () => {
		return save()
	}

	const saveDraft = () => {
		return save('draft')
	}

	const saveAutosave = () => {
		return save('autosave')
	}

	return {
		savePage,
		saveDraft,
		saveAutosave,
		isSavePageLoading
	}
}