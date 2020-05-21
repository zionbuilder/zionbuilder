import * as types from '../mutation-types'

const state = {
	notices: []
}

const getters = {
	getErrors: state => state.notices
}

const actions = {
	addNotice: ({ commit }, payload) => {
		commit(types.ADD_ERROR, payload)
	},
	removeNotice: ({ commit }, payload) => {
		commit(types.REMOVE_NOTICE, payload)
	}
}

const mutations = {
	[types.ADD_ERROR] (state, payload) {
		state.notices.push(payload)
	},
	[types.REMOVE_NOTICE] (state, payload) {
		let index = state.notices.indexOf(payload)
		if (index !== -1) {
			state.notices.splice(index, 1)
		}
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
