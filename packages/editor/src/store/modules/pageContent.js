import * as types from '../mutation-types'
import { generateUID, deepCopy, updateOptionValue, getOptionValue } from '@zb/utils'
import { savePage } from '@zb/rest'
import Cache from '../../Cache.ts'

const copyElementContent = (elementConfig, newChildsObject, getters) => {
	const newChilds = newChildsObject || {}
	const copiedElement = deepCopy(elementConfig)

	copiedElement.uid = generateUID()

	if (copiedElement.content && copiedElement.content.length > 0) {
		const newUids = []
		copiedElement.content.forEach((childElementUid) => {
			const childElementData = getters.getElementData(childElementUid)

			if (childElementData) {
				// Modify child element data
				const childCopy = copyElementContent(childElementData, newChilds, getters)
				newUids.push(childCopy.copiedElement.uid)
				newChilds[childCopy.copiedElement.uid] = childCopy.copiedElement
			}
		})

		if (newUids.length > 0) {
			copiedElement.content = newUids
		}
	}

	return {
		copiedElement,
		newChilds
	}
}

const state = {
	pageAreas: {
		content: []
	},
	pageContent: {},
	activeArea: null,
	activeElement: null,
	pageId: window.ZnPbInitalData.page_id,
	copiedClasses: null,
	copiedElement: null,
	rightClickMenu: null,
	cuttedElement: null,
	template_categories: window.ZnPbInitalData.template_categories,
	isPageDirty: false,
	activeElementUid: null

}

const getParents = function (currentUid, allContent, parent) {
	let parentsToReturn = parent
	Object.keys(allContent).forEach(elementUid => {
		const elementData = allContent[elementUid]
		if (elementData.content.includes(currentUid)) {
			const foundParent = {
				uid: elementData.uid,
				children: []
			}
			// add break
			// if we found our match
			if (parent) {
				foundParent.children.push(parent)
			}

			parentsToReturn = getParents(elementData.uid, allContent, foundParent)
		}
	})

	return parentsToReturn
}

const getters = {
	getPageContent: state => state.pageContent,
	getActiveElementOptionValue: (state, getters) => (path, defaultValue) => {
		if (!state.activeElementUid) {
			return defaultValue
		}

		return getOptionValue(getters.getActiveElementData.options, path, defaultValue)
	},
	getElementOptionValue: (state, getters) => (elementUid, path, defaultValue = null) => {
		const elementData = getters.getElementData(elementUid)

		if (!elementData) {
			return defaultValue
		}

		return getOptionValue(elementData.options, path, defaultValue)
	},
	getCopiedClasses: state => state.copiedClasses
}

let droppingTimeout = null

const actions = {
	setActiveElement ({ commit }, elementUid) {
		commit(types.SET_ACTIVE_ELEMENT, elementUid)
	},
	setCopiedElement ({ commit }, payload) {
		commit(types.SET_COPIED_ELEMENT, payload)
	},
	setCuttedElement ({ commit }, payload) {
		commit(types.SET_CUTTED_ELEMENT, payload)
	},
	setCopiedClasses ({ commit }, payload) {
		commit(types.SET_COPIED_CLASSES, payload)
	},
	setPageContent: ({ commit }, payload) => {
		commit(types.SET_PAGE_CONTENT, payload)
	},
	setPageAreas: ({ commit }, payload) => {
		commit(types.SET_PAGE_AREAS, payload)
	},
	setActiveArea: ({ commit }, payload) => {
		commit(types.SET_ACTIVE_AREA, payload)
	},
	setContentState ({ commit, dispatch, state }, newState) {
		// Check to see if an item which is no longer present exists
		if (state.activeElementUid) {
			if (!newState.pageContent[state.activeElementUid]) {
				// Close the panel
				window.zb.editor.panels.closePanel('PanelElementOptions')
				dispatch('setActiveElement', null)
			}
		}

		// Cleanup copied elements and styles to prevent errors
		commit(types.SET_COPIED_CLASSES, null)
		commit(types.SET_COPIED_ELEMENT, null)
		commit(types.SET_INITIAL_PAGE_CONTENT, newState)
	},

	insertElements: ({ commit, dispatch, getters }, payload) => {
		const { parentUid, index, childElements, parentElements } = payload
		const parentContent = getters.getElementData(parentUid).content

		commit(types.ADD_ELEMENTS, {
			elements: childElements
		})

		commit(types.INSERT_ELEMENTS_IN_PARENT, {
			parentContent,
			index,
			elements: parentElements
		})

		// Add to history
		const currentTime = new Date()
		dispatch('addToHistory', {
			name: `Added columns layout`,
			time: `${currentTime.getHours()}:${currentTime.getMinutes()}`,
			state: state
		})
	},

	deleteElement: ({ commit, getters, dispatch, state }, { elementUid, parentUid }) => {
		const elementConfig = getters.getElementData(elementUid)
		const elementSavedName = getters.getElementName(elementUid)

		// If the active element is deleted, also remove the active element
		if (state.activeElementUid && state.activeElementUid === elementConfig.uid) {
			// close panel
			window.zb.editor.panels.closePanel('PanelElementOptions')
			dispatch('setActiveElement', null)
		} else {
			const childsToDelete = getChildElementsUids(elementConfig.content, state.pageContent)
			if (childsToDelete.includes(state.activeElementUid)) {
				// close panel
				window.zb.editor.panels.closePanel('PanelElementOptions')
				dispatch('setActiveElement', null)
			}
		}

		commit(types.DELETE_ELEMENT, { elementUid, parentUid })

		// Add to history
		const currentTime = new Date()
		dispatch('addToHistory', {
			name: `Deleted ${elementSavedName}`,
			time: `${currentTime.getHours()}:${currentTime.getMinutes()}`,
			state: state
		})
	}
}

const getChildElementsUids = function (elementContent, pageContent) {
	const childs = []

	if (elementContent.length > 0) {
		childs.push(...elementContent)

		// Check for nested elements
		elementContent.forEach(elementUid => {
			const elementData = pageContent[elementUid]
			const childElementsUids = getChildElementsUids(elementData.content, pageContent)
			childs.push(...childElementsUids)
		})
	}

	return childs
}

const mutations = {
	[types.SET_TEMPLATE_CATEGORIES] (state, payload) {
		state.template_categories.unshift(payload)
	},
	[types.SET_ACTIVE_ELEMENT] (state, elementUid) {
		state.activeElementUid = elementUid
	},
	[types.SET_RIGHT_CLICK_MENU] (state, payload) {
		state.rightClickMenu = payload
	},
	[types.SET_COPIED_ELEMENT_STYLES] (state, payload) {
		state.copiedElementStyles = payload
	},
	[types.SET_COPIED_ELEMENT] (state, payload) {
		state.copiedElement = payload
	},
	[types.SET_CUTTED_ELEMENT] (state, payload) {
		state.cuttedElement = payload
	},
	[types.SET_COPIED_CLASSES] (state, payload) {
		state.copiedClasses = payload
	},
	[types.SET_INITIAL_PAGE_CONTENT] (state, payload) {
		Object.assign(state, payload)
	},
	[types.SET_PAGE_CONTENT] (state, payload) {
		state.pageContent = payload
	},
	[types.SET_PAGE_AREAS] (state, payload) {
		state.pageAreas = payload
	},
	[types.SET_ACTIVE_AREA] (state, payload) {
		state.activeArea = payload
	},
	[types.SAVE_ELEMENTS_ORDER] (state, { newOrder, content }) {
		content.splice(0, content.length, ...newOrder)
	},
	[types.RENAME_ELEMENT] (state, { elementUid, elementName }) {
		const elementConfig = state.pageContent[elementUid]
		const newValues = updateOptionValue(elementConfig.options, '_advanced_options._element_name', elementName)

		elementConfig.options = newValues
	},
	[types.UPDATE_ELEMENT_OPTIONS] (state, { element, values }) {
		// element.options = values
	},

	[types.DELETE_ELEMENT_OPTION] (state, path) {
		const paths = path.split('.')
		const activeElement = state.pageContent[state.activeElementUid]

		if (typeof activeElement === 'undefined') {
			return
		}

		// const options = activeElement.options
		let upperDeletableKey = null

		paths.reduce((acc, key, index) => {
			if (index === paths.length - 1) {
				if (upperDeletableKey) {
					const { acc, key } = upperDeletableKey
					delete acc[key]
				} else {
					delete acc[key]
				}

				return true
			}

			// Check to see if we can delete an upper value
			if (typeof acc[key] === 'object' && acc[key] !== null && Object.keys(acc[key]).length === 1) {
				if (!upperDeletableKey) {
					upperDeletableKey = {
						acc,
						key
					}
				}
			} else {
				upperDeletableKey = null
			}

			acc[key] = acc[key] ? acc[key] : {}
			return acc[key]
		}, options)
	},

	[types.DELETE_ELEMENT] (state, { elementUid, parentUid }) {
		const elementData = state.pageContent[elementUid]
		const parentElementData = state.pageContent[parentUid]
		const childsToDelete = getChildElementsUids(elementData.content, state.pageContent)

		let indexInParentContent = parentElementData.content.indexOf(elementUid)

		// Delete from parent content
		parentElementData.content.splice(indexInParentContent, 1)

		delete state.pageContent[elementUid]
		// childsToDelete.push(elementUid)

		// Check for children
		childsToDelete.forEach((childUid) => {
			delete state.pageContent[childUid]
		})
	},
	[types.MOVE_ELEMENT] (state, { elementUid, newParentUid, oldParentUid, newIndex }) {
		const newParentElementData = state.pageContent[newParentUid]
		const oldParentElementData = state.pageContent[oldParentUid]
		const oldIndexLocation = oldParentElementData.content.indexOf(elementUid)

		oldParentElementData.content.splice(oldIndexLocation, 1)
		newParentElementData.content.splice(newIndex, 0, elementUid)
	},
	[types.ADD_ELEMENT] (state, payload) {
		const { parent, index = -1, data } = payload

		const uid = generateUID()
		const defaults = {
			element_type: null,
			options: {},
			content: []
		}

		const elementConfig = {
			...defaults,
			...data,
			uid
		}

		state.pageContent[uid] = elementConfig
		if (parent) {
			parent.splice(index, 0, uid)
		}
	},
	[types.ADD_ELEMENTS] (state, payload) {
		const { elements } = payload

		if (Object.keys(elements).length === 0) {
			return
		}

		const newElements = {
			...state.pageContent,
			...elements
		}

		state.pageContent = newElements
	},
	[types.INSERT_ELEMENTS_IN_PARENT] (state, payload) {
		const { index, parentContent, elements } = payload
		if (index === -1) {
			parentContent.push(...elements)
		} else {
			parentContent.splice(index, 0, ...elements)
		}
	},
	[types.ATTACH_SLOTS_TO_ELEMENT] (state, payload) {
		const { elementData, slots } = payload
		if (elementData && slots) {
			elementData.slots = slots
		}
	}
}

export default {
	getters,
	actions,
	mutations,
	state
}
