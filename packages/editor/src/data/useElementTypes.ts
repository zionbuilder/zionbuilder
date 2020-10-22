import { find } from 'lodash-es'
import { ref, Ref, computed } from 'vue'
import { ElementType } from './models'

const zionElements = window.ZnPbInitalData.elements_data.map(config => {
	return new ElementType(config)
})

// Add content Wrapper
zionElements.push(new ElementType({
	element_type: 'contentRoot',
	wrapper: true
}))

const elementTypes: Ref = ref(zionElements)

export function useElementTypes() {
	const addElementType = (config) => {
		elementTypes.value.push(new ElementType(config))
	}

	const getVisibleElements = computed(() => {
		return elementTypes.value.filter((element) => {
			return element.show_in_ui
		})
	})

	const getElementType = (elementType: string) => {
		return find(elementTypes.value, {element_type: elementType})
	}

	const registerElementComponent = (component) => {
		const element = getElementType(component.name)

		if (!element) {
			console.warn(`element with ${component.name} could not be found.`)
		}

		element.registerComponent(component)
	}

	return {
		elementTypes,
		getVisibleElements,
		addElementType,
		getElementType,
		registerElementComponent
	}
}