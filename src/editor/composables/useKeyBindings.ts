import { useSavePage, useElementActions } from '../composables';
import { useHistoryStore, useUIStore, useUserStore } from '../store';

const { isEditable, Environment } = window.zb.utils;

export const useKeyBindings = () => {
	const UIStore = useUIStore();
	const userStore = useUserStore();
	const { savePage, isSavePageLoading } = useSavePage();
	const { copyElement, pasteElement, resetCopiedElement, copyElementStyles, pasteElementStyles } = useElementActions();

	const controlKey = Environment.isMac ? 'metaKey' : 'ctrlKey';

	// const getNextFocusedElement = element => {
	// 	const parentContent = element.parent.content;
	// 	const elementIndex = parentContent.indexOf(element);
	// 	const previousElement = parentContent[elementIndex - 1];
	// 	const nextElement = parentContent[elementIndex + 1];

	// 	if (previousElement) {
	// 		return previousElement;
	// 	} else if (nextElement) {
	// 		return nextElement;
	// 	} else if (element.parent && element.parent.element_type !== 'contentRoot') {
	// 		return element.parent;
	// 	}

	// 	return null;
	// };

	// end checkMousePosition
	const applyShortcuts = (e: KeyboardEvent) => {
		// Save CTRL+S
		if (e.which === 83 && e[controlKey] && !e.shiftKey) {
			e.preventDefault();
			if (!isSavePageLoading.value) {
				savePage();
			}
		}

		// Set preview mode
		if (e.which === 80 && e[controlKey]) {
			UIStore.setPreviewMode(!UIStore.isPreviewMode);
			e.preventDefault();
		}

		if (isEditable()) {
			return;
		}

		// Keys bellow don't run in preview mode
		if (UIStore.isPreviewMode) {
			return;
		}

		// Shortcuts that needs an active element
		if (UIStore.editedElement && !userStore.permissions.only_content) {
			const activeElementFocus = UIStore.editedElement;

			// Duplicate - CTRL+D
			if (e.which === 68 && e[controlKey] && !e.shiftKey) {
				activeElementFocus.duplicate();
				e.preventDefault();
			}

			// copy
			if (e.which === 67 && e[controlKey] && !e.shiftKey) {
				copyElement(activeElementFocus);
			}

			// Paste
			if (e.which === 86 && e[controlKey] && !e.shiftKey) {
				pasteElement(activeElementFocus);
			}

			// Cut - CTRL + X
			if (e.which === 88 && e[controlKey]) {
				copyElement(activeElementFocus, 'cut');
			}

			if (e.code === 'Escape') {
				resetCopiedElement();
			}

			// Delete element
			if (e.which === 46 || (Environment.isMac && e.which === 8)) {
				activeElementFocus.delete();
			}

			// Copy element styles ctrl+shift+c
			if (e[controlKey] && e.shiftKey && e.which === 67) {
				copyElementStyles(activeElementFocus);
				e.preventDefault();
			}

			// Paste element styles ctrl + shift + v
			if (e[controlKey] && e.shiftKey && e.which === 86) {
				pasteElementStyles(activeElementFocus);
				e.preventDefault();
			}

			// Hide element/panel
			if (e.which === 72 && e[controlKey]) {
				if (activeElementFocus) {
					activeElementFocus.setVisibility(!activeElementFocus.isVisible);
					e.preventDefault();
				}
			}
		}

		// Undo CTRL+Z
		if (e.which === 90 && e[controlKey] && !e.shiftKey) {
			const historyStore = useHistoryStore();

			if (historyStore.canUndo) {
				historyStore.undo();
			}
			e.preventDefault();
		}

		// Redo CTRL+SHIFT+D -- Back to WP Dashboard
		if (e.code === 'KeyD' && e[controlKey] && e.shiftKey) {
			window.open(window.ZnPbInitialData.urls.edit_page, '_blank');
		}

		// Redo CTRL+SHIFT+Z CTRL + Y
		if ((e.which === 90 && e[controlKey] && e.shiftKey) || (e[controlKey] && e.which === 89)) {
			const historyStore = useHistoryStore();
			if (historyStore.canRedo) {
				historyStore.redo();
			}

			e.preventDefault();
		}

		// Toggle treeView panel
		if (e.shiftKey && e.code === 'KeyT') {
			UIStore.togglePanel('panel-tree');
			e.preventDefault();
		}

		// Opens Library
		if (e.shiftKey && e.code === 'KeyL') {
			UIStore.toggleLibrary();
			e.preventDefault();
		}

		// Opens Page options
		if (e.shiftKey && e.code === 'KeyO') {
			UIStore.togglePanel('panel-global-settings');
			e.preventDefault();
		}
	};

	return {
		applyShortcuts,
	};
};
