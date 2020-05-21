import * as types from '../mutation-types'
import Vue from 'vue'
import { replaceUrl } from '@/api/ReplaceUrl'

const state = {
	url: [],
	loaded: false
}

const getters = {
	getUrl: state => state.url
}

const actions = {
	replaceUrl: ({ commit }, paths) => {
		if (!state.loaded) {
			return replaceUrl(paths).then((response) => {
				commit(types.SET_NEW_URL, response.data)
				commit(types.SET_NEW_URL_LOADED_STATE, true)
			})
		}
	}
}

const mutations = {
	[types.SET_NEW_URL] (state, payload) {
		Vue.set(state, 'url', payload)
	},
	[types.SET_NEW_URL_LOADED_STATE] (state, payload) {
		Vue.set(state, 'loaded', payload)
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
