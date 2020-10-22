import * as types from '../mutation-types'

const elements = window.ZnPbInitalData.elements_data.map(element => {
	return Object.freeze(element)
})

const elementCategories = window.ZnPbInitalData.elements_categories.map(category => {
	return Object.freeze(category)
})

const state = {
	elements_categories: elementCategories,
	registeredElements: elements,
	visibleElements: [],
	elementAdvancedOptionsSchema: Object.freeze(window.ZnRestConfig.schemas.element_advanced),
	elementStyleOptionsSchema: Object.freeze(window.ZnRestConfig.schemas.styles)
}

const getters = {
	getRegisteredElements: state => state.registeredElements,
	getElementCategories: state => state.elements_categories,
	getElementById: (state) => (elementType) => {
		const element = state.registeredElements.find((element) => element.element_type === elementType)
		if (element === undefined) {
			// eslint-disable-next-line
			console.warn('Could not retrieve the element ' + elementType)
			return {
				options: {}
			}
		}

		return element
	},
	getElementsByCategory: (state) => (categoryId) => {
		let filteredElements = []

		if (categoryId === 'all') {
			return state.registeredElements
		}

		state.registeredElements.forEach((element) => {
			if (element.category !== undefined && element.category === categoryId) {
				filteredElements.push(element)
			}
		})

		return filteredElements
	},
	getVisibleElements: (state) => {
		return state.visibleElements
	},
	getElementAdvancedOptionsSchema: state => state.elementAdvancedOptionsSchema,
	getElementStyleOptionsSchema: state => state.elementStyleOptionsSchema
}

const actions = {
	registerElement: ({ commit }, elementConfig) => {
		commit(types.REGISTER_ELEMENT, elementConfig)
	},
}

const mutations = {
	[types.REGISTER_ELEMENT] (state, elementConfig) {
		state.registeredElements.push(elementConfig)
	},
	[types.SET_VISIBLE_ELEMENTS] (state, visibleElements) {
		state.visibleElements = visibleElements
	}
}

export default {
	state,
	getters,
	actions,
	mutations
}
