import * as types from '../mutation-types'

const state = {
	panelsState: [
		{
			name: '',
			id: 'PanelElementOptions',
			position: 'relative',
			isDetached: false,
			isExpanded: false,
			isActive: false,
			group: 'only-one',
			background: '#302c36',
			width: {
				value: 360,
				unit: 'px'
			},
			height: {
				value: null,
				unit: 'auto'
			},
			panelPos: 2
		},
		{
			name: '',
			id: 'panel-global-settings',
			position: 'relative',
			isDetached: false,
			isExpanded: false,
			isActive: false,
			group: 'only-one',
			background: '#302c36',
			width: {
				value: 360,
				unit: 'px'
			},
			height: {
				value: null,
				unit: 'auto'
			},
			panelPos: 3
		},
		{
			name: '',
			id: 'panel-tree',
			position: 'relative',
			isDetached: false,
			isExpanded: false,
			background: '#302c36',
			isActive: false,
			width: {
				value: 360,
				unit: 'px'
			},
			height: {
				value: null,
				unit: 'auto'
			},
			panelPos: 4
		},
		{
			name: '',
			id: 'panel-history',
			position: 'relative',
			isDetached: false,
			isExpanded: false,
			background: '#302c36',
			isActive: false,
			width: {
				value: 360,
				unit: 'px'
			},
			height: {
				value: null,
				unit: 'auto'
			},
			panelPos: 5
		},

		{
			name: '',
			id: 'PanelLibraryModal',
			position: 'fixed',
			isDetached: false,
			isExpanded: false,
			isActive: false,
			width: {
				value: 1440,
				unit: 'px'
			},
			height: {
				value: null,
				unit: 'auto'
			},
			panelPos: null
		}

	],
	openPanels: [],
	panelPlaceholder: {},
	activePanel: null,
	isAnyPanelDragging: false
}

const getters = {
	getOpenedPanels: state => state.openPanels,
	getPanelConfig: state => state.panelsState.find((panelsState) => {
		return state.panelsState.id === state.currentState
	}),
	getStateList: state => {
		return state.panelsState
	},
	getPanelPlaceholder: state => {
		return state.panelPlaceholder
	},
	getActivePanel: state => {
		return state.activePanel
	},
	getIsAnyPanelDragging: state => {
		return state.isAnyPanelDragging
	}
}

const actions = {
	setIsAnyPanelDragging: ({ commit }, payload) => {
		commit(types.SET_IS_ANY_PANEL_DRAGGING, payload)
	},
	setActivePanel: ({ commit, state }, payload) => {
		commit(types.SET_ACTIVE_PANEL, payload)
	},
	setPanelPlaceholder: ({ commit, state }, payload) => {
		commit(types.SET_PANEL_PLACEHOLDER, payload)
	},
	setPanelProp: ({ commit, state }, payload) => {
		commit(types.SET_PANEL_PROP, payload)
	},
	openPanel: ({ commit, state }, panelId) => {
		// Get panel config from panelName
		let panelConfig = state.panelsState.find((panelConfig) => {
			return panelConfig.id === panelId
		})

		// If this panel is part of a group,
		// close other panels from the same group that are already opened
		if (panelConfig && typeof panelConfig.group !== 'undefined') {
			state.openPanels.forEach(panel => {
				if (typeof panel.group !== 'undefined' && panel.group === panelConfig.group && panel !== panelConfig) {
					commit(types.CLOSE_PANEL, panel)
				}
			})
		}
		// Check to see if the panel is already opened
		if (!state.openPanels.includes(panelConfig)) {
			commit(types.OPEN_PANEL, panelConfig)
		}

		// Set the active panel
		commit(types.SET_ACTIVE_PANEL, panelConfig.id)
	},
	closePanel: ({ commit }, panelId) => {
		// Get panel config from panelName
		let panelConfig = state.panelsState.find((panelConfig) => {
			return panelConfig.id === panelId
		})
		commit(types.CLOSE_PANEL, panelConfig)
	},
	togglePanel: ({ commit, state, dispatch }, panelId) => {
		// Get panel config from panelName
		let panelConfig = state.panelsState.find((panelConfig) => {
			return panelConfig.id === panelId
		})

		if (state.openPanels.includes(panelConfig)) {
			dispatch('closePanel', panelConfig.id)
		} else {
			dispatch('openPanel', panelConfig.id)
		}
	},
	setPanelWidth: ({ commit }, { panelConfig, panelWidth }) => {
		commit(types.SET_PANEL_WIDTH, { panelConfig, panelWidth })
	},
	savePanelsOrder: ({ commit }, payload) => {
		commit(types.SET_PANELS_ORDER, payload)
	},
	setPanelPos: ({ commit, dispatch }, { panelId, panelPos }) => {
		commit(types.SET_PANEL_POS, { panelId, panelPos })
	}
}

const mutations = {
	// Change editor height

	[types.SET_IS_ANY_PANEL_DRAGGING] (state, payload) {
		state.isAnyPanelDragging = payload
	},
	[types.SET_ACTIVE_PANEL] (state, payload) {
		state.activePanel = payload
	},
	[types.SET_PANEL_PLACEHOLDER] (state, payload) {
		state.panelPlaceholder = payload
	},
	[types.SET_PANEL_PROP] (state, payload) {
		const { id, prop, value } = payload
		let index = state.panelsState.findIndex(k => k.id === id)
		state.panelsState[index][prop] = value
	},
	[types.OPEN_PANEL] (state, panelConfig) {
		state.openPanels.push(panelConfig)
	},
	[types.CLOSE_PANEL] (state, panelConfig) {
		let openPanelIndex = state.openPanels.indexOf(panelConfig)
		if (openPanelIndex > -1) {
			state.openPanels.splice(openPanelIndex, 1)
		}
	},
	[types.SET_PANEL_WIDTH] (state, { panelConfig, panelWidth }) {
		let index = state.panelsState.indexOf(panelConfig)
		state.panelsState[index].width.value = panelWidth
	},
	[types.SET_PANELS_ORDER] (state, payload) {
		state.openPanels = payload
	},
	[types.SET_PANEL_POS] (state, { panelId, panelPos }) {
		let index = state.panelsState.findIndex(k => k.id === panelId)
		state.panelsState[index].panelPos = panelPos
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
