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
	visibleElements: []
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
	}
}

const actions = {
	registerElement: ({ commit }, elementConfig) => {
		commit(types.REGISTER_ELEMENT, elementConfig)
	},
	filterElementsBySearch ({ commit, state }, searchTerm) {
		if (searchTerm === null) {
			commit(types.SET_VISIBLE_ELEMENTS, [])
		} else {
			let visibleElements = Object.assign(
				[],
				state.registeredElements.filter(element => {
					return (
						element.name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1 ||
						element.keywords
							.join()
							.toLowerCase()
							.indexOf(searchTerm.toLowerCase()) !== -1
					)
				})
			)

			commit(types.SET_VISIBLE_ELEMENTS, visibleElements)
		}
	},

	filterElementsByLevel ({ commit, state }, elementDropLevel) {
		let visibleElements = Object.assign(
			[],
			state.registeredElements.filter(element => {
				// Columns
				if (elementDropLevel === 1) {
					return parseInt(element.level) === 2
				} else if (elementDropLevel === 2) {
					// Level 3 elements
					return parseInt(element.level) === 3
				} else {
					// level 1 elements
					return parseInt(element.level) === 3 || parseInt(element.level) === 1
				}
			})
		)

		commit(types.SET_VISIBLE_ELEMENTS, visibleElements)
	}
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
