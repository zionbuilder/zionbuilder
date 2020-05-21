import * as types from '../mutation-types'
import Vue from 'vue'
import { getSystemInfo } from '@/api/SystemInfo'

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
		Vue.set(state, 'systemInfo', payload)
	},
	[types.SET_SYSTEM_INFO_LOADED_STATE] (state, payload) {
		Vue.set(state, 'loaded', payload)
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
