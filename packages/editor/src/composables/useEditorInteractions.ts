import { ref, Ref, reactive } from 'vue'
import { EditorArea } from './models'

const defaultiFrame = {
	pointerEvents: false,
	order: 6
}

const mainBar = reactive({
	position: 'left',
	pointerEvents: false,
	draggingPosition: null,

})
const iFrameInstance = new EditorArea(defaultiFrame)
const iFrame = ref(iFrameInstance)

export function useEditorInteractions() {
	const getMainbarPosition = () => {
		return mainBar.position
	}

	const getMainBarPointerEvents = () => {
		return mainBar.pointerEvents
	}

	const getIframePointerEvents = () => {
		return iFrame.value['pointerEvents']
	}
	const getIframeOrder = () => {
		return iFrame.value['order']
	}

	return {
		getMainbarPosition,
		getMainBarPointerEvents,
		getIframePointerEvents,
		getIframeOrder,
		mainBar,
		iFrame

	}
}