import { savePage as savePageREST } from '@zb/rest'
import Cache from '../Cache'
import { ref, Ref } from 'vue'
import { useTemplateParts } from './useTemplateParts'
import { usePageSettings } from './usePageSettings'
import { useCSSClasses } from './useCSSClasses'
import { useEditorData } from './useEditorData'
import { translate } from '@zb/i18n'
import { useNotifications } from '@zionbuilder/composables'

const isSavePageLoading: Ref<boolean> = ref(false)

export function useSavePage() {
	const save = (status = 'publish') => {
		const { add } = useNotifications()
		const { getActivePostTemplatePart } = useTemplateParts()
		const contentTemplatePart = getActivePostTemplatePart()
		const { pageSettings } = usePageSettings()
		const { CSSClasses } = useCSSClasses()
		const { editorData } = useEditorData()
		const pageID = editorData.value.page_id

		if (!contentTemplatePart) {
			console.error('Content template data not found.')
			return
		}

		const pageData = {
			page_id: editorData.value.page_id,
			template_data: contentTemplatePart.toJSON(),
			page_settings: pageSettings.value,
			css_classes: CSSClasses.value
		}

		// Check if this is a draft
		if (status) {
			pageData.status = status
		}

		if (status !== 'autosave') {
			isSavePageLoading.value = true
		}

		return new Promise((resolve, reject) => {
			savePageREST(pageData)
				.then((response) => {
					if (status !== 'autosave') {
						add({
							message: status === 'publish' ? translate('page_saved_publish') : translate('page_saved'),
							delayClose: 5000,
							type: 'success'
						})
					}
					Cache.deleteItem(pageID)
					return Promise.resolve(response)
				})
				.catch(error => {
					Cache.saveItem(pageID, pageData)

					add({
						message: error.message,
						type: 'error',
						delayClose: 5000
					})

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