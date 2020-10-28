import { debounce } from 'lodash-es'
import { usePanels, usePreviewMode, useElementFocus, useSavePage } from '@data'

export const useKeyBindings = () => {
	const { openPanel, closePanel, openPanels, togglePanel } = usePanels()
	const { isPreviewMode, setPreviewMode } = usePreviewMode()
	const { focusedElement, focusElement } = useElementFocus()
	const { savePage, isSavePageLoading } = useSavePage()

	const debounceDelete = debounce(function (element) {
		const parentContent = element.parent.content
		const elementIndex = parentContent.indexOf(element.uid)
		const previousElement = parentContent[elementIndex - 1]
		const nextElement = parentContent[elementIndex + 1]

		if (previousElement) {
			focusElement(previousElement)
		} else if (nextElement) {
			focusElement(nextElement)
		} else {
			focusElement(element.parent)
		}

		element.delete()
	})

	const debouncePaste = debounce(function (uid, parentUid, insertParent) {
		const copiedElement = this.getCopiedElement
		const cuttedElement = this.getCuttedElement
		if (copiedElement) {
			this.copyElement({
				elementUid: copiedElement.uid,
				pasteElementUid: uid,
				insertParent: copiedElement.isWrapper ? parentUid : insertParent,
				parentUid: insertParent
			})

			this.setElementFocus({
				uid: copiedElement.uid,
				parentUid: parentUid,
				insertParent: insertParent
			})
		}

		if (cuttedElement) {
			const newParent = insertParent
			const parentContent = this.getElementData(newParent).content
			const newIndex = parentContent.indexOf(uid) + 1

			this.moveElement({
				elementUid: cuttedElement.uid,
				oldParentUid: cuttedElement.parentUid,
				newParentUid: newParent,
				newIndex
			})

			this.setElementFocus({
				uid: cuttedElement.uid,
				parentUid: parentUid,
				insertParent
			})
			this.setCuttedElement(null)
		}
	})

	const debounceDuplicate = debounce(function (element) {
		element.duplicate()
	})

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
				debounceDuplicate(activeElementFocus)
				e.preventDefault()
			}

			// copy
			if (e.which === 67 && e.ctrlKey && !e.shiftKey) {
				if (!e.target.getAttribute('contenteditable')) {
					const elementId = this.getElementData(activeElementFocus.uid).element_type
					this.setCopiedElement({
						uid: activeElementFocus.uid,
						isWrapper: this.getElementById(elementId).wrapper
					})
					this.setCuttedElement(null)
				}
			}

			// Paste
			if (e.which === 86 && e.ctrlKey && !e.shiftKey) {
				if (!e.target.getAttribute('contenteditable')) {
					if ((this.getCopiedElement || this.getCuttedElement) && activeElementFocus.insertParent) {
						this.debouncePaste(activeElementFocus.uid, activeElementFocus.parentUid, activeElementFocus.insertParent)
					}
				}
			}
			// Cut - CTRL + X
			if (e.which === 88 && e.ctrlKey) {
				if (!e.target.getAttribute('contenteditable')) {
					const elementId = this.getElementData(activeElementFocus.uid).element_type
					this.setCuttedElement({
						uid: activeElementFocus.uid,
						parentUid: activeElementFocus.parentUid,
						insertParent: activeElementFocus.insertParent,
						isWrapper: this.getElementById(elementId).wrapper
					})
					this.setCopiedElement(null)
				}
			}

			if (e.code === 'Escape' && this.getCuttedElement) {
				this.setCuttedElement(null)
			}

			// Delete element
			if (e.which === 46) {
				if (!e.target.getAttribute('contenteditable')) {
					debounceDelete(activeElementFocus)
				}
			}

			// Copy element styles ctrl+shift+c
			if (e.ctrlKey && e.shiftKey && e.which === 67) {
				e.preventDefault()
				copiedStyles = activeElementFocus.elementData.options._styles
			}

			// Paste element styles ctrl + shift + v
			if (e.ctrlKey && e.shiftKey && e.which === 86) {
				this.updateElementOptions({
					elementUid: activeElementFocus.uid,
					values: {
						...activeElementFocus.elementData.options,
						_styles: copiedStyles
					}
				})
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
				getEditPageUrl,
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