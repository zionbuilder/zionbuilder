import { ref, Ref, readonly } from 'vue'
import { PageElement } from '../store2'

const focusedElementRef: Ref = ref(null)

export const focusedElement: Ref<PageElement> = readonly(focusedElementRef)

export const setFocusedElement = (element: PageElement) => {
	focusedElementRef.value = element
}

export const deleteFocusedElement = () => {
	focusedElementRef.value = null
}