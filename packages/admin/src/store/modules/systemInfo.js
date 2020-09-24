import * as types from '../mutation-types'
import { getSystemInfo } from '@zb/rest'

const state = {
	systemInfo: [],
	loaded: false
}

const getters = {
	getSystemInfo: state => state.systemInfo
}

const actions = {
	fetchSystemInfo: ({ commit, state }) => {
		if (!state.loaded) {
			return getSystemInfo().then((response) => {
				commit(types.SET_SYSTEM_INFO, response.data)
				commit(types.SET_SYSTEM_INFO_LOADED_STATE, true)
			})
		}
	}
}

const mutations = {
	[types.SET_SYSTEM_INFO] (state, payload) {
		state.systemInfo = payload
	},
	[types.SET_SYSTEM_INFO_LOADED_STATE] (state, payload) {
		state.loaded = payload
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
