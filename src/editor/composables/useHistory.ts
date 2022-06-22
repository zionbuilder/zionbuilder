import { ref, Ref, computed } from 'vue';
import { each } from 'lodash-es';
import { useTemplateParts } from './useTemplateParts';
import { usePageSettingsStore, useCSSClassesStore } from '../store';
import { useEditorData } from './useEditorData';
import { translate } from '/@/common/modules/i18n';
import { useUIStore } from '../store';

const historyItems: Ref = ref([]);
const currentHistoryIndex: Ref = ref(-1);
const isDirty: Ref<boolean> = ref(false);

export function useHistory() {
	const canUndo = computed(() => {
		return currentHistoryIndex.value > 0;
	});
	const canRedo = computed(() => {
		return currentHistoryIndex.value < historyItems.value.length - 1;
	});

	function undo() {
		if (currentHistoryIndex.value - 1 >= 0) {
			currentHistoryIndex.value = currentHistoryIndex.value - 1;
			restoreHistoryState(currentHistoryIndex.value);
			setDirtyStatus(true);
		}
	}

	function redo() {
		if (currentHistoryIndex.value + 1 <= historyItems.value.length - 1) {
			currentHistoryIndex.value = currentHistoryIndex.value + 1;
			restoreHistoryState(currentHistoryIndex.value);

			setDirtyStatus(true);
		}
	}

	function addToHistory(name: string, ignoreDirty = false) {
		// Get the state
		const state = getDataForSave();
		const currentTime = new Date();

		if (!state) {
			return;
		}

		const historyData = {
			state,
			name,
			time: `${currentTime.getHours()}:${currentTime.getMinutes()}`,
		};

		currentHistoryIndex.value += 1;

		// If this is a new change tree, remove the old one
		if (currentHistoryIndex.value !== historyItems.value.length) {
			const itemsToRemove = historyItems.value.length - currentHistoryIndex.value;
			historyItems.value.splice(currentHistoryIndex.value, itemsToRemove, historyData);
		} else {
			historyItems.value.push(historyData);
		}

		// Set the page as dirty so we know that we have changes
		if (!ignoreDirty) {
			setDirtyStatus(true);
		}
	}

	function setDirtyStatus(status) {
		isDirty.value = status;
	}

	function restoreHistoryState(index: number) {
		const data = historyItems.value[index];

		if (!data) {
			console.warn('No data found for the selected history item.');
			return;
		}

		const { editorData } = useEditorData();
		const { registerTemplatePart } = useTemplateParts();

		const content = {
			[editorData.value.page_id]: data.state.template_data,
		};

		// New system
		each(content, (value, id) => {
			const area = registerTemplatePart({
				name: id,
				id: id,
			});

			area.element.addChildren(value);
		});

		currentHistoryIndex.value = index;

		// Close element options panel
		useUIStore.unEditElement();

		setDirtyStatus(true);
	}

	function addInitialHistory() {
		addToHistory(translate('initial_state'), true);
	}

	function getDataForSave() {
		const { getActivePostTemplatePart } = useTemplateParts();
		const contentTemplatePart = getActivePostTemplatePart();
		const pageSettings = usePageSettingsStore();
		const cssClasses = useCSSClassesStore();
		const { editorData } = useEditorData();

		if (!contentTemplatePart) {
			console.error('Content template data not found.');
			return;
		}

		return JSON.parse(
			JSON.stringify({
				page_id: editorData.value.page_id,
				template_data: contentTemplatePart.toJSON(),
				page_settings: pageSettings.settings,
				css_classes: cssClasses.CSSClasses,
			}),
		);
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
		redo,
		setDirtyStatus,
		isDirty,
	};
}
