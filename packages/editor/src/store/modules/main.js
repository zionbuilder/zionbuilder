import * as types from '../mutation-types'

const state = {
	isStyleLoading: true,
	lockedUserInfo: window.ZnPbInitalData.post_lock_user,
	is_pro_active: window.ZnPbInitalData.plugin_info.is_pro_active,

}

const getters = {

	isPro: state => state.is_pro_active,
}

const actions = {
	setLockedUser: ({ commit }, payload) => {
		commit(types.SET_LOCKED_USERINFO, payload)
	},
	setNonce: ({ commit }, payload) => {
		commit(types.SET_NONCE, payload)
	}
}

const mutations = {
	[types.SET_STYLE_LOADING] (state, payload) {
		state.isStyleLoading = payload
	},
	[types.SET_LOCKED_USERINFO] (state, payload) {
		state.lockedUserInfo = payload
	},
	[types.TAKE_OVER_POST] (state, payload) {
		state.lockedUserInfo = {}
	},
	[types.SET_NONCE] (state, payload) {
		state.nonce = payload
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
