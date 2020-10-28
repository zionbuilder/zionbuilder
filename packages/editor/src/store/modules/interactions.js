import * as types from '../mutation-types'

const state = {
	isDragging: false,
	isSavingPage: false,
	isPreviewMode: false,
	pseudoSelectors: [
		{
			name: 'default',
			id: 'default'
		},
		{
			name: ':hover',
			id: ':hover'
		},
		{
			name: ':before',
			id: ':before'
		},
		{
			name: ':after',
			id: ':after'
		},
		{
			name: ':active',
			id: ':active'
		},
		{
			name: ':focus',
			id: ':focus'
		},
		{
			name: ':custom',
			id: 'custom'
		}
	],
	activePseudoSelector: null,
	// Will open the addElements popup
	shouldOpenAddElementsPopup: false,
	// Keeps a track of element focus
	elementFocus: {
		uid: null,
		parent: null,
		insertParent: null,
		scrollIntoView: false
	},
	activeShowAddElementsPopup: null

}

const getters = {
	isDragging: state => state.isDragginggetElementFocus
}

const actions = {
	/**
	 * Shows the add elements popup for an element
	 */
	setActiveShowElementsPopup: ({ commit }, payload) => {
		commit(types.SET_ACTIVE_SHOW_ADD_ELEMENT_POPUP, payload)
	},
	setDraggingState: ({ commit }, payload) => {
		commit(types.SET_DRAGGING_STATE, payload)
	},
	setActivePseudoSelector: ({ commit }, payload) => {
		commit(types.SET_ACTIVE_PSEUDO_SELECTOR, payload)
	},
	setNewSelector: ({ commit }, payload) => {
		commit(types.SET_NEW_SELECTOR, payload)
	},
	deleteNewSelector: ({ commit }, payload) => {
		commit(types.DELETE_NEW_SELECTOR, payload)
	},
	setShouldOpenAddElementsPopup: ({ commit }, payload) => {
		commit(types.SET_SHOULD_OPEN_ELEMENTS_POPUP, payload)
	},
	setElementFocus: ({ commit, getters }, payload) => {
		if (payload) {
			const { uid } = payload

			// Get the options for the active element
			const elementData = getters.getElementData(uid)

			// Don't proceed if we have no config for this element
			if (elementData) {
				payload = {
					...payload,
					elementData
				}
			}
		}

		commit(types.SET_ELEMENT_FOCUS, payload)
	}
}

const mutations = {
	[types.SET_PREVIEW_MODE] (state, payload) {
		state.isPreviewMode = payload
	},
	[types.SET_IS_SAVING_PAGE] (state, payload) {
		state.isSavingPage = payload
	},
	[types.SET_DRAGGING_STATE] (state, payload) {
		state.isDragging = payload
	},
	[types.SET_ACTIVE_PSEUDO_SELECTOR] (state, payload) {
		state.activePseudoSelector = payload
	},
	[types.SET_NEW_SELECTOR] (state, payload) {
		state.pseudoSelectors.push(payload)
	},
	[types.DELETE_NEW_SELECTOR] (state, payload) {
		const selectorIndex = state.pseudoSelectors.indexOf(payload)
		state.pseudoSelectors.splice(selectorIndex, 1)
	},
	[types.SET_SHOULD_OPEN_ELEMENTS_POPUP] (state, payload) {
		state.shouldOpenAddElementsPopup = payload
	},
	[types.SET_ELEMENT_FOCUS] (state, payload) {
		state.elementFocus = payload
	},
	[types.SET_ACTIVE_SHOW_ADD_ELEMENT_POPUP] (state, payload) {
		state.activeShowAddElementsPopup = payload
	}

}

export default {
	state,
	getters,
	actions,
	mutations
}
