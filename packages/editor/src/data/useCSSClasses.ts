import { ref, Ref } from 'vue'

const CSSClasses = ref(window.ZnPbInitalData.css_classes || [])

export const useCSSClasses = () => {

	return {
		CSSClasses
	}
}