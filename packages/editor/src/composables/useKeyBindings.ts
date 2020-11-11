import { debounce } from 'lodash-es'
import { usePanels, usePreviewMode, useSavePage, useEditorData, useElementActions } from '@composables'

export const useKeyBindings = () => {
	const { openPanels, togglePanel } = usePanels()
	const { isPreviewMode, setPreviewMode } = usePreviewMode()
	const { savePage, isSavePageLoading } = useSavePage()
	const { copyElement, pasteElement, copiedElement, resetCopiedElement, copyElementStyles, pasteElementStyles, focusedElement, focusElement } = useElementActions()
	const { editorData } = useEditorData()

	const debounceUndo = debounce(function () {
		if (this.canUndo) {
			this.undo()
		}
	})

	const debounceRedo = debounce(function () {
		if (this.canRedo) {
			this.redo()
		}
	})

	const getNextFocusedElement = (element) => {
		const parentContent = element.parent.content
		const elementIndex = parentContent.indexOf(element.uid)
		const previousElement = parentContent[elementIndex - 1]
		const nextElement = parentContent[elementIndex + 1]

		if (previousElement) {
			return previousElement
		} else if (nextElement) {
			return nextElement
		} else if (element.parent && element.parent.element_type !== 'contentRoot') {
			return element.parent
		}

		return null
	}

	// end checkMousePosition
	const applyShortcuts = (e) => {
		if (e.target.isContentEditable) {
			return
		}

		// Set preview mode
		if (e.which === 80 && e.ctrlKey) {
			setPreviewMode(!isPreviewMode.value)
			e.preventDefault()
		}

		// Keys bellow don't run in preview mode
		if (isPreviewMode.value) {
			return
		}

		// Shortcuts that needs an active element
		if (focusedElement.value) {
			const activeElementFocus = focusedElement.value

			// Duplicate - CTRL+D
			if (e.which === 68 && e.ctrlKey && !e.shiftKey) {
				activeElementFocus.duplicate()
				e.preventDefault()
			}

			// copy
			if (e.which === 67 && e.ctrlKey && !e.shiftKey) {
				if (!e.target.getAttribute('contenteditable')) {
					copyElement(activeElementFocus)
				}
			}

			// Paste
			if (e.which === 86 && e.ctrlKey && !e.shiftKey && copiedElement.value.element) {
				if (!e.target.getAttribute('contenteditable')) {
					pasteElement(activeElementFocus)
				}
			}
			// Cut - CTRL + X
			if (e.which === 88 && e.ctrlKey) {
				if (!e.target.getAttribute('contenteditable')) {
					copyElement(activeElementFocus, 'cut')
				}
			}

			if (e.code === 'Escape' && copiedElement.value.element) {
				resetCopiedElement()
			}

			// Delete element
			if (e.which === 46) {
				if (!e.target.getAttribute('contenteditable')) {
					const nextFocusElement = getNextFocusedElement(activeElementFocus)
					activeElementFocus.delete()
					focusElement(nextFocusElement)
				}
			}

			// Copy element styles ctrl+shift+c
			if (e.ctrlKey && e.shiftKey && e.which === 67) {
				copyElementStyles(activeElementFocus)
				e.preventDefault()
			}

			// Paste element styles ctrl + shift + v
			if (e.ctrlKey && e.shiftKey && e.which === 86) {
				pasteElementStyles(activeElementFocus)
				e.preventDefault()
			}

			// Hide element/panel
			if (e.which === 72 && e.ctrlKey) {
				if (activeElementFocus) {
					activeElementFocus.toggleVisibility()
					e.preventDefault()
				}
			}

		}

		// Undo CTRL+Z
		const isPanelElementOptionsOpen = openPanels.value.filter(panel => panel.isActive)
		if (!isPanelElementOptionsOpen) {
			if (e.which === 90 && e.ctrlKey && !e.shiftKey) {
				debounceUndo()
			}
		}

		// Redo CTRL+SHIFT+D -- Back to WP Dashboard
		if (e.code === 'KeyD' && e.ctrlKey && e.shiftKey) {
			window.open(
				editorData.value.urls.edit_page,
				'_blank'
			)
		}
		// Redo CTRL+SHIFT+Z CTRL + Y
		if (!isPanelElementOptionsOpen) {
			if ((e.which === 90 && e.ctrlKey && e.shiftKey) || (e.ctrlKey && e.which === 89)) {
				debounceRedo()
			}
		}

		// Save CTRL+S
		if (e.which === 83 && e.ctrlKey && !e.shiftKey) {
			e.preventDefault()
			if (!isSavePageLoading.value) {
				savePage()
			}
		}

		// Toggle treeView panel
		if (e.shiftKey && e.code === 'KeyT') {
			togglePanel('panel-tree')
			e.preventDefault()
		}

		// Opens Library
		if (e.shiftKey && e.code === 'KeyL') {
			togglePanel('PanelLibraryModal')
			e.preventDefault()
		}

		// Opens Page options
		if (e.shiftKey && e.code === 'KeyO') {
			togglePanel('panel-global-settings')
			e.preventDefault()
		}
	}

	return {
		applyShortcuts
	}
}