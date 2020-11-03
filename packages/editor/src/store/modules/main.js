import * as types from '../mutation-types'

const state = {
	mainBar: {
		position: 'left',
		order: -999,
		pointerEvents: false
	},
	isStyleLoading: true,
	iFrame: {
		pointerEvents: false,
		order: 6
	},
	urls: window.ZnPbInitalData.urls,
	lockedUserInfo: window.ZnPbInitalData.post_lock_user,
	is_pro_active: window.ZnPbInitalData.plugin_info.is_pro_active,

}

const getters = {

	isPro: state => state.is_pro_active,
	getLogoUrl: state => state.urls.logo,
	getLoadingUrl: state => state.urls.loader,
	getStylesLoading: state => state.isStyleLoading,
	getMainbarPosition: state => {
		return state.mainBar.position
	},
	getMainBarOrder: state => {
		return state.mainBar.order
	},
	getMainBarPointerEvents: state => {
		return state.mainBar.pointerEvents
	},
	getIframePointerEvents: state => {
		return state.iFrame.pointerEvents
	},
	getIframeOrder: state => {
		return state.iFrame.order
	}
}

const actions = {
	setMainbarPosition: ({ commit }, payload) => {
		commit(types.SET_MAINBAR_POSITION, payload)
	},
	setMainbarOrder: ({ commit, state, dispatch }, order) => {
		commit(types.SET_MAINBAR_ORDER, order)
	},
	setMainBarPointerEvents: ({ commit, state }, pointerEvents) => {
		commit(types.SET_MAINBAR_POINTER_EVENTS, pointerEvents)
	},
	setIframePointerEvents: ({ commit, state }, pointerEvents) => {
		commit(types.SET_IFRAME_POINTER_EVENTS, pointerEvents)
	},
	setIframeOrder: ({ commit, state, dispatch }, order) => {
		commit(types.SET_IFRAME_ORDER, order)
	},
	setStylesLoading: ({ commit }, payload) => {
		commit(types.SET_STYLE_LOADING, payload)
	},
	setLockedUser: ({ commit }, payload) => {
		commit(types.SET_LOCKED_USERINFO, payload)
	},
	setNonce: ({ commit }, payload) => {
		commit(types.SET_NONCE, payload)
	}
}

const mutations = {
	[types.SET_MAINBAR_ORDER] (state, order) {
		state.mainBar.order = order
	},
	[types.SET_MAINBAR_POSITION] (state, payload) {
		state.mainBar.position = payload
	},
	[types.SET_MAINBAR_POINTER_EVENTS] (state, pointerEvents) {
		state.mainBar.pointerEvents = pointerEvents
	},
	[types.SET_IFRAME_POINTER_EVENTS] (state, pointerEvents) {
		state.iFrame.pointerEvents = pointerEvents
	},
	[types.SET_IFRAME_ORDER] (state, order) {
		state.iFrame.order = order
	},
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
