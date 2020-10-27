import * as types from '../mutation-types'

const state = {
	classes: window.ZnPbInitalData.css_classes || []
}

const getters = {
	getClassesByFilter: (state) => (keyword) => {
		const keyToLower = keyword.toLowerCase()
		return state.classes.filter(className => className.name.toLowerCase().indexOf(keyToLower) !== -1 || className.id.toLowerCase().indexOf(keyToLower) !== -1)
	},
	getClassConfig: (state) => (classId) => {
		return state.classes.find((classConfig) => {
			return classConfig.id === classId
		})
	}
}

const actions = {
	addClass: ({ commit, state }, cssClass) => {
		commit(types.ADD_CSS_CLASS, cssClass)
	},
	editClass: ({ commit, state }, { cssClassIndex, newval }) => {
		commit(types.EDIT_CSS_CLASS, { cssClassIndex, newval })
	},
	removeClass: ({ commit }, cssClass) => {
		commit(types.REMOVE_CSS_CLASS, cssClass)
	},
	updateClassSettings: ({ commit }, payload) => {
		commit(types.UPDATE_CSS_CLASS, payload)
	}
}

const mutations = {
	[types.SET_ACTIVE_CSS_CLASS] (state, activeClass) {
		state.classes = activeClass
	},
	[types.ADD_CSS_CLASS] (state, cssClass) {
		const newClass = {
			name: cssClass,
			id: cssClass,
			style: {}
		}
		state.classes.push(newClass)
	},

	[types.EDIT_CSS_CLASS] (state, { cssClassIndex, newval }) {
		state.classes[cssClassIndex] = newval
	},
	[types.REMOVE_CSS_CLASS] (state, cssClass) {
		const cssClassIndex = state.classes.indexOf(cssClass)
		state.classes.splice(cssClassIndex, 1)
	},
	[types.UPDATE_CSS_CLASS] (state, { classId, newValues }) {
		const editedClass = state.classes.find((cssClassConfig) => {
			return cssClassConfig.id === classId
		})

		if (!editedClass) {
			// eslint-disable-next-line
			console.warn('could not find class with config ', { classId, newValues })
			return
		}

		const cssClassIndex = state.classes.indexOf(editedClass)
		const updatedValues = {
			...editedClass,
			...newValues
		}

		state.classes[cssClassIndex] = updatedValues
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
