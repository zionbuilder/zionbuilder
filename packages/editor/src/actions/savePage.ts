import { savePage as savePageREST } from '@zb/rest'
import Cache from '../Cache.ts'
import { store } from '../store'

export const savePage = (content, status) => {
	const pageID = store.getters.getPageId
	const pageData = {
		page_id: pageID,
		template_data: content,
		// page_settings: getters.getPageSettings,
		// css_classes: getters.getClasses
	}

	// Check if this is a draft
	if (status) {
		pageData.status = status
	}

	return savePageREST(pageData).catch(error => {
		Cache.saveItem(pageID, content)
		// eslint-disable-next-line
		console.error(error)
	}).finally(() => {
		// dispatch('setIsSavingPage', false)
		resolve()
	})
}