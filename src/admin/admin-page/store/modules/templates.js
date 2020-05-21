import Vue from 'vue'
import * as types from '../mutation-types'
import {
	getTemplates,
	deleteTemplate,
	addTemplate
} from '@/api/Templates'

const state = {
	templates: [],
	loaded: false
}

const getters = {
	getTemplates: state => state.templates

}
const actions = {
	fetchTemplates: ({ commit, state }, force = false) => {
		if (!force && state.loaded) {
			// eslint-disable-next-line new-cap
			return Promise.resolve()
		}

		return getTemplates().then((response) => {
			commit(types.SET_LOCAL_TEMPLATES, response.data)
			commit(types.TEMPLATE_SET_LOADED, true)
		})
	},
	deleteTemplate: ({ commit }, template) => {
		// call to server
		return deleteTemplate(template.ID).then(() => {
			// On deleted remove from store
			commit(types.DELETE_TEMPLATE, template)
		})
	},

	addTemplate: ({ commit }, newTemplate) => {
		addTemplate(newTemplate).then((response) => {
			commit(types.ADD_TEMPLATE, response.data)
		})
	}
}

const mutations = {
	[types.SET_LOCAL_TEMPLATES] (state, payload) {
		Vue.set(state, 'templates', payload)
	},
	[types.DELETE_TEMPLATE] (state, template) {
		const templateIndex = state.templates.indexOf(template)
		state.templates.splice(templateIndex, 1)
	},
	[types.ADD_TEMPLATE] (state, newTemplate) {
		state.templates.push(newTemplate)
	},
	[types.TEMPLATE_SET_LOADED] (state, loadingState) {
		state.loaded = loadingState
	}

}

export default {
	state,
	getters,
	actions,
	mutations
}
