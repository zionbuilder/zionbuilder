import { ref, Ref } from 'vue'

const CSSClasses = ref(window.ZnPbInitalData.css_classes || [])

export const useCSSClasses = () => {

	const getClassesByFilter = (keyword) => {
		const keyToLower = keyword.toLowerCase()
		return CSSClasses.value.filter(className => className.name.toLowerCase().indexOf(keyToLower) !== -1 || className.id.toLowerCase().indexOf(keyToLower) !== -1)
	}

	const getClassConfig = (classId) => {
		return CSSClasses.value.find((classConfig) => {
			return classConfig.id === classId
		})
	}

	const addCSSClass = (cssClass) => {
		const newClass = {
			name: cssClass,
			id: cssClass,
			style: {}
		}
		CSSClasses.value.push(newClass)
	}

	const removeCSSClass = (cssClass) => {
		const cssClassIndex = CSSClasses.value.indexOf(cssClass)
		CSSClasses.value.splice(cssClassIndex, 1)
	}

	const updateCSSClass = (classId, newValues) => {
		const editedClass = CSSClasses.value.find((cssClassConfig) => {
			return cssClassConfig.id === classId
		})

		if (!editedClass) {
			// eslint-disable-next-line
			console.warn('could not find class with config ', { classId, newValues })
			return
		}

		const cssClassIndex = CSSClasses.value.indexOf(editedClass)
		const updatedValues = {
			...editedClass,
			...newValues
		}

		CSSClasses.value[cssClassIndex] = updatedValues
	}

	function removeAllCssClasses() {
		CSSClasses.value = []
	}

	function setCSSClasses(newValue) {
		CSSClasses.value = newValue
	}

	return {
		CSSClasses,
		getClassesByFilter,
		getClassConfig,
		addCSSClass,
		removeCSSClass,
		updateCSSClass,
		removeAllCssClasses,
		setCSSClasses
	}
}