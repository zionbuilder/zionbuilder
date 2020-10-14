import { each, find } from 'lodash-es'
import { ref, Ref } from 'vue'
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

	const getElementType = (elementType: string) => {
		return find(elementTypes.value, {element_type: elementType})
	}

	return {
		addElementType,
		getElementType
	}
}