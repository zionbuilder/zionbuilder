import * as types from '../mutation-types'
import { deepCopy } from '@/utils/utils'
import Cache from '@/editor/Cache'

const state = {
	historyItems: [],
	currentHistoryIndex: -1
}

const getters = {
	getHistoryItems: state => state.historyItems,
	canUndo: state => state.currentHistoryIndex > 0,
	canRedo: state => state.currentHistoryIndex < state.historyItems.length - 1,
	activeHistoryIndex: state => state.currentHistoryIndex
}

const actions = {
	addToHistory: ({ commit, getters, state }, payload) => {
		// Check to see if this is not the latest state
		const isNewChange = state.currentHistoryIndex + 1 !== state.historyItems.length

		if (!payload.time) {
			const currentTime = new Date()
			payload.time = `${currentTime.getHours()}:${currentTime.getMinutes()}:${currentTime.getSeconds()}`
		}

		commit(types.HISTORY_CHANGE_ACTIVE, state.currentHistoryIndex + 1)
		commit(types.HISTORY_ADD, { payload, isNewChange })

		const pageId = getters.getPageId

		Cache.saveItem(pageId, payload.state)
	},
	setInitialHistory: ({ rootState, commit, state }, name) => {
		// Add to history
		const currentTime = new Date()
		const payload = {
			state: rootState.pageContent,
			name,
			time: `${currentTime.getHours()}:${currentTime.getMinutes()}:${currentTime.getSeconds()}`
		}

		commit(types.HISTORY_CHANGE_ACTIVE, state.currentHistoryIndex + 1)
		commit(types.HISTORY_ADD, {
			payload,
			isNewChange: true
		})
	},

	// Save the page content state
	saveState: ({ rootState, dispatch }, name) => {
		dispatch('addToHistory', {
			name,
			state: rootState.pageContent
		})
	},
	restoreHistoryState: ({ commit, getters, dispatch }, historyIndex) => {
		const historyState = state.historyItems[historyIndex]

		// Build new state containing changes
		dispatch('setContentState', JSON.parse(JSON.stringify(historyState.state)))
		commit(types.HISTORY_CHANGE_ACTIVE, historyIndex)
	},
	undo: ({ commit, state, dispatch }) => {
		const historyIndex = state.currentHistoryIndex - 1

		dispatch('setContentState', JSON.parse(JSON.stringify(state.historyItems[historyIndex].state)))
		commit(types.HISTORY_CHANGE_ACTIVE, historyIndex)
	},
	redo: ({ commit, dispatch }) => {
		const historyIndex = state.currentHistoryIndex + 1

		dispatch('setContentState', JSON.parse(JSON.stringify(state.historyItems[historyIndex].state)))
		commit(types.HISTORY_CHANGE_ACTIVE, historyIndex)
	}
}

const mutations = {
	[types.HISTORY_ADD] (state, { payload, isNewChange }) {
		const historyState = deepCopy(payload.state)

		// Don't store the active element
		const { pageAreas, pageContent } = historyState
		const historyItem = {
			...payload,
			state: {
				pageAreas,
				pageContent
			}
		}

		if (isNewChange) {
			const itemsToRemove = state.historyItems.length - state.currentHistoryIndex
			state.historyItems.splice(state.currentHistoryIndex, itemsToRemove, historyItem)
		} else {
			state.historyItems.push(historyItem)
		}
	},
	[types.HISTORY_CHANGE_ACTIVE] (state, historyIndex) {
		state.currentHistoryIndex = historyIndex
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
