import { ref, Ref } from 'vue'
import { EditorArea } from './models'
const defaultMainBar = {
	position: 'left',
	order: -999,
	pointerEvents: false
}
const defaultiFrame = {
	pointerEvents: false,
	order: 6
}
const mainBarInstance = new EditorArea(defaultMainBar)

const mainBar = ref(mainBarInstance)
const iFrameInstance = new EditorArea(defaultiFrame)

const iFrame = ref(iFrameInstance)

export function useEditorInteractions() {

	const getMainbarPosition = () => {
		return mainBar.value.position
	}

	const getMainBarOrder = () => {
		return mainBar.value.order
	}

	const getMainBarPointerEvents = () => {
		return mainBar.value.pointerEvents
	}

	const getIframePointerEvents = () => {
		return iFrame.value['pointerEvents']
	}
	const getIframeOrder = () => {
		return iFrame.value['order']
	}

	return {
		getMainbarPosition,
		getMainBarOrder,
		getMainBarPointerEvents,
		getIframePointerEvents,
		getIframeOrder,
		mainBar,
		iFrame

	}
}