import * as types from '../mutation-types'

const state = {
	activeDevice: 'default',
	devices: [
		{
			name: 'Desktop',
			id: 'default',
			width: {
				value: '100',
				unit: '%'
			},
			height: {
				value: '100',
				unit: '%'
			},
			icon: 'desktop'
		},
		{
			name: 'Laptop',
			id: 'laptop',
			width: {
				value: '991',
				unit: 'px'
			},
			height: {
				value: '768',
				unit: 'px'
			},
			units: 'px',
			icon: 'laptop'
		},
		{
			name: 'Tablet',
			id: 'tablet',
			width: {
				value: '767',
				unit: 'px'
			},
			height: {
				value: '1024',
				unit: 'px'
			},
			icon: 'tablet'
		},
		{
			name: 'Mobile',
			id: 'mobile',
			width: {
				value: '575',
				unit: 'px'
			},
			height: {
				value: '667',
				unit: 'px'
			},
			units: 'px',
			icon: 'mobile'
		}
	]

}

const getters = {
	getActiveDevice: state => state.devices.find((device) => {
		return device.id === state.activeDevice
	}),
	getDeviceList: state => {
		return state.devices
	}
}

const actions = {
	setActiveDevice: ({ commit, state }, payload) => {
		if (state.activeDevice !== payload) {
			commit(types.SET_ACTIVE_DEVICE, payload)
		}
	}
}

const mutations = {
	// Set a new active device
	[types.SET_ACTIVE_DEVICE] (state, payload) {
		state.activeDevice = payload
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
