import { find } from 'lodash-es'
import { ref, Ref, computed } from 'vue'
import { ElementType } from './models'
import { translate } from '@zb/i18n'

const elementTypes: Ref = ref([])

elementTypes.value.push(new ElementType({
	element_type: 'contentRoot',
	wrapper: true,
	show_in_ui: false
}))

export function useElementTypes() {
	function addElementTypes(elements: []) {
		elements.forEach(elementConfig => {
			elementTypes.value.push(new ElementType(elementConfig))
		})
	}

	const addElementType = (config) => {
		elementTypes.value.push(new ElementType(config))
	}

	const getVisibleElements = computed(() => {
		return elementTypes.value.filter((element) => {
			return element.show_in_ui
		})
	})

	const getElementType = (elementType: string) => {
		return find(elementTypes.value, { element_type: elementType }) || new ElementType({
			element_type: 'invalid',
			name: translate('invalid_element'),
		})
	}

	const registerElementComponent = (config) => {
		const { elementType, component } = config
		const element = getElementType(elementType)

		if (!element) {
			console.warn(`element with ${elementType} could not be found.`)
			return
		}

		element.registerComponent(component)
	}

	const getElementIcon = (elementType: string) => {
		let element = getElementType(elementType)
		return element.icon ? element.icon : null
	}

	const getElementImage = (elementType: string) => {
		let element = getElementType(elementType)
		return element.thumb ? element.thumb : null
	}

	function resetElementComponents() {
		elementTypes.value.forEach(elementType => {
			elementType.resetComponent()
		})
	}

	return {
		elementTypes,
		getVisibleElements,
		getElementIcon,
		getElementImage,
		addElementType,
		getElementType,
		registerElementComponent,
		resetElementComponents,
		addElementTypes
	}
}