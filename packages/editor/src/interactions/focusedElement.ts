import { ref, Ref, readonly } from 'vue'

const focusedElementRef: Ref = ref(null)

export const focusedElement: Ref = readonly(focusedElementRef)

export const setFocusedElement = (element) => {
	focusedElementRef.value = element
}

export const deleteFocusedElement = () => {
	focusedElementRef.value = null
}