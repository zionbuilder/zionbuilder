import { ref, Ref, computed } from 'vue'
import { each } from 'lodash-es'
import { useTemplateParts } from './useTemplateParts'
import { usePageSettings } from './usePageSettings'
import { useCSSClasses } from './useCSSClasses'
import { useEditorData } from './useEditorData'
import { translate } from '@zb/i18n'
import Cache from '../Cache'
import { useEditElement } from './useEditElement'

const historyItems: Ref = ref([])
const currentHistoryIndex: Ref = ref(-1)

export function useHistory() {
	const canUndo = computed(() => {
		return currentHistoryIndex.value > 0
	})
	const canRedo = computed(() => {
		return currentHistoryIndex.value < historyItems.value.length - 1
	})

	function undo () {
		if (currentHistoryIndex.value - 1 >= 0) {
			currentHistoryIndex.value = currentHistoryIndex.value - 1
			restoreHistoryState(currentHistoryIndex.value)
		}
	}

	function redo() {
		if (currentHistoryIndex.value + 1 <= historyItems.value.length - 1) {
			currentHistoryIndex.value = currentHistoryIndex.value +1
			restoreHistoryState(currentHistoryIndex.value)
		}
	}

	function addToHistory(name: string) {
		// Get the state
		const state = getDataForSave()
		const currentTime = new Date()

		if (!state) {
			return
		}

		historyItems.value.push({
			state,
			name,
			time: `${currentTime.getHours()}:${currentTime.getMinutes()}`
		})
		currentHistoryIndex.value += 1

		Cache.saveItem(state.page_id, state)
	}

	function restoreHistoryState(index: number) {
		const data = historyItems.value[index]

		if (!data) {
			console.warn('No data found for the selected history item.')
			return
		}

		const { registerTemplatePart } = useTemplateParts()

		const content = {
			content: data.state.template_data
		}

		// New system
		each(content, (value, id) => {
			const area = registerTemplatePart({
				name: id,
				id: id
			})

			area.element.addChildren(value)
		})

		currentHistoryIndex.value = index

		// Close element options panel
		const { unEditElement } = useEditElement()
		unEditElement()
	}

	function addInitialHistory() {
		addToHistory( translate('initial_state') )
	}

	function getDataForSave() {
		const { getTemplatePart } = useTemplateParts()
		const contentTemplatePart = getTemplatePart('content')
		const { pageSettings } = usePageSettings()
		const { CSSClasses } = useCSSClasses()
		const { editorData } = useEditorData()

		if (!contentTemplatePart) {
			console.error('Content template data not found.')
			return
		}

		return JSON.parse(JSON.stringify({
			page_id: editorData.value.page_id,
			template_data: contentTemplatePart.toJSON(),
			page_settings: pageSettings.value,
			css_classes: CSSClasses.value
		}))
	}


	return {
		historyItems,
		currentHistoryIndex,
		canUndo,
		canRedo,
		addInitialHistory,
		addToHistory,
		restoreHistoryState,
		undo,
		redo
	}

}
