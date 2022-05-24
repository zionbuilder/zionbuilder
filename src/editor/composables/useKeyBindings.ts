import {
	usePreviewMode,
	useSavePage,
	useEditorData,
	useElementActions,
	useHistory,
	useEditElement,
} from '../composables';
import { isEditable, Environment } from '@common/utils';
import { useUIStore } from '../store';

export const useKeyBindings = () => {
	const { togglePanel, toggleLibrary } = useUIStore();
	const { isPreviewMode, setPreviewMode } = usePreviewMode();
	const { savePage, isSavePageLoading } = useSavePage();
	const { copyElement, pasteElement, resetCopiedElement, copyElementStyles, pasteElementStyles } = useElementActions();
	const { editorData } = useEditorData();
	const { editElement, element: focusedElement } = useEditElement();

	const controllKey = Environment.isMac ? 'metaKey' : 'ctrlKey';

	const getNextFocusedElement = element => {
		const parentContent = element.parent.content;
		const elementIndex = parentContent.indexOf(element);
		const previousElement = parentContent[elementIndex - 1];
		const nextElement = parentContent[elementIndex + 1];

		if (previousElement) {
			return previousElement;
		} else if (nextElement) {
			return nextElement;
		} else if (element.parent && element.parent.element_type !== 'contentRoot') {
			return element.parent;
		}

		return null;
	};

	// end checkMousePosition
	const applyShortcuts = e => {
		// Save CTRL+S
		if (e.which === 83 && e[controllKey] && !e.shiftKey) {
			e.preventDefault();
			if (!isSavePageLoading.value) {
				savePage();
			}
		}

		// Set preview mode
		if (e.which === 80 && e[controllKey]) {
			setPreviewMode(!isPreviewMode.value);
			e.preventDefault();
		}

		if (isEditable()) {
			return;
		}

		// Keys bellow don't run in preview mode
		if (isPreviewMode.value) {
			return;
		}

		// Shortcuts that needs an active element
		if (focusedElement.value) {
			const activeElementFocus = focusedElement.value;

			// Duplicate - CTRL+D
			if (e.which === 68 && e[controllKey] && !e.shiftKey) {
				activeElementFocus.duplicate();
				e.preventDefault();
			}

			// copy
			if (e.which === 67 && e[controllKey] && !e.shiftKey) {
				copyElement(activeElementFocus);
			}

			// Paste
			if (e.which === 86 && e[controllKey] && !e.shiftKey) {
				pasteElement(activeElementFocus);
			}

			// Cut - CTRL + X
			if (e.which === 88 && e[controllKey]) {
				copyElement(activeElementFocus, 'cut');
			}

			if (e.code === 'Escape') {
				resetCopiedElement();
			}

			// Delete element
			if (e.which === 46 || (Environment.isMac && e.which === 8)) {
				const nextFocusElement = getNextFocusedElement(activeElementFocus);

				activeElementFocus.delete();

				if (nextFocusElement) {
					editElement(nextFocusElement);
				}
			}

			// Copy element styles ctrl+shift+c
			if (e[controllKey] && e.shiftKey && e.which === 67) {
				copyElementStyles(activeElementFocus);
				e.preventDefault();
			}

			// Paste element styles ctrl + shift + v
			if (e[controllKey] && e.shiftKey && e.which === 86) {
				pasteElementStyles(activeElementFocus);
				e.preventDefault();
			}

			// Hide element/panel
			if (e.which === 72 && e[controllKey]) {
				if (activeElementFocus) {
					activeElementFocus.toggleVisibility();
					e.preventDefault();
				}
			}
		}

		// Undo CTRL+Z
		if (e.which === 90 && e[controllKey] && !e.shiftKey) {
			const { canUndo, undo } = useHistory();

			if (canUndo.value) {
				undo();
			}
		}

		// Redo CTRL+SHIFT+D -- Back to WP Dashboard
		if (e.code === 'KeyD' && e[controllKey] && e.shiftKey) {
			window.open(editorData.value.urls.edit_page, '_blank');
		}

		// Redo CTRL+SHIFT+Z CTRL + Y
		if ((e.which === 90 && e[controllKey] && e.shiftKey) || (e[controllKey] && e.which === 89)) {
			const { canRedo, redo } = useHistory();
			if (canRedo.value) {
				redo();
			}
		}

		// Toggle treeView panel
		if (e.shiftKey && e.code === 'KeyT') {
			togglePanel('panel-tree');
			e.preventDefault();
		}

		// Opens Library
		if (e.shiftKey && e.code === 'KeyL') {
			toggleLibrary();
			e.preventDefault();
		}

		// Opens Page options
		if (e.shiftKey && e.code === 'KeyO') {
			togglePanel('panel-global-settings');
			e.preventDefault();
		}
	};

	return {
		applyShortcuts,
	};
};
