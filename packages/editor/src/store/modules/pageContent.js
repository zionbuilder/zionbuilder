import * as types from '../mutation-types'
import { generateUID, deepCopy, updateOptionValue, getOptionValue } from '@zb/utils'
import { savePage } from '@zb/rest'
import Cache from '../../Cache.ts'

const state = {
	pageAreas: {
		content: []
	},
	pageContent: {},
	activeArea: null,
	activeElement: null,
	pageId: window.ZnPbInitalData.page_id,
	copiedClasses: null,
	copiedElement: null,
	rightClickMenu: null,
	cuttedElement: null,
	template_categories: window.ZnPbInitalData.template_categories,
	isPageDirty: false,
	activeElementUid: null

}

const getters = {
	getCopiedClasses: state => state.copiedClasses
}

const actions = {
	setCopiedClasses ({ commit }, payload) {
		commit(types.SET_COPIED_CLASSES, payload)
	}
}

const mutations = {}

export default {
	getters,
	actions,
	mutations,
	state
}
