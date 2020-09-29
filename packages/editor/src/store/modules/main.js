import * as types from '../mutation-types'
import Vue from 'vue'

const state = {
	mainBar: {
		position: 'left',
		order: -999,
		pointerEvents: false
	},
	isStyleLoading: true,
	isPreviewLoading: true,
	iFrame: {
		pointerEvents: false,
		order: 6
	},
	urls: window.ZnPbInitalData.urls,
	lockedUserInfo: window.ZnPbInitalData.post_lock_user,
	nonce: window.ZnRestConfig.nonce,
	is_pro_active: window.ZnPbInitalData.plugin_info.is_pro_active,
	masks: window.ZnPbInitalData.masks
}

const getters = {
	getMasks: state => state.masks,
	getAssetsUrl: state => state.urls.assets_url,
	isPro: state => state.is_pro_active,
	getLogoUrl: state => state.urls.logo,
	getLoadingUrl: state => state.urls.loader,
	isPreviewLoading: state => state.isPreviewLoading,
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
	},
	getPreviewFrameUrl: state => state.urls.preview_frame_url,
	getPreviewUrl: state => state.urls.preview_url,
	getEditPageUrl: state => state.urls.edit_page,
	getZionAdminUrl: state => state.urls.zion_admin,
	getAllPagesUrl: state => state.urls.all_pages_url,
	getLockedUserInfo: state => state.lockedUserInfo,
	isPostLocked: state => state.lockedUserInfo && !!state.lockedUserInfo.message,
	getNonce: state => state.nonce
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
	setPreviewFrameLoading: ({ commit }, payload) => {
		commit(types.SET_PREVIEW_FRAME_LOADING, payload)
	},
	setStylesLoading: ({ commit }, payload) => {
		commit(types.SET_STYLE_LOADING, payload)
	},
	setLockedUser: ({ commit }, payload) => {
		commit(types.SET_LOCKED_USERINFO, payload)
	},
	takeOverPost: ({ commit }) => {
		commit(types.TAKE_OVER_POST)
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
	[types.SET_PREVIEW_FRAME_LOADING] (state, payload) {
		state.isPreviewLoading = payload
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
