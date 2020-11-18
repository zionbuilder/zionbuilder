import { ref, Ref, computed } from 'vue'
import { each } from 'lodash-es'
import { useTemplateParts } from './useTemplateParts'
import { usePageSettings } from './usePageSettings'
import { useCSSClasses } from './useCSSClasses'
import { useEditorData } from './useEditorData'
import { translate } from '@zb/i18n'
import Cache from '../Cache'

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

	}

	function redo() {

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

		return {
			page_id: editorData.value.page_id,
			template_data: contentTemplatePart.toJSON(),
			page_settings: pageSettings.value,
			css_classes: CSSClasses.value
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
	}

	function addInitialHistory() {
		addToHistory( translate('initial_state') )
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
