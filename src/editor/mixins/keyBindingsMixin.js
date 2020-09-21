import { mapGetters, mapActions } from 'vuex'
import { debounce } from 'lodash-es'
import { getElement } from '@zb/editor/elements'

let copiedStyles = {}
let openedPanels = []

export default {
	data () {
		return {
			isPanelElementOptionsOpen: false,
			isDisplayingSaveNotice: false
		}
	},
	computed: {
		...mapGetters([
			'getElementFocus',
			'getElementData',
			'canUndo',
			'canRedo',
			'getCopiedElement',
			'isPreviewMode',
			'getOpenedPanels',
			'getCuttedElement',
			'getEditPageUrl'
		])
	},
	watch: {
		isPreviewMode (newValue) {
			if (!newValue) {
				// restore panels
				openedPanels.forEach((panelId) => {
					this.openPanel(panelId)
				})
			} else {
				// Set panels
				openedPanels.forEach((panelId) => {
					this.closePanel(panelId)
				})
			}
		},
		getOpenedPanels (newValue) {
			newValue.forEach(panel => {
				this.isPanelElementOptionsOpen = panel.id === 'PanelElementOptions'
			})
			if (!newValue.length) {
				this.isPanelElementOptionsOpen = false
			}
			if (!this.isPreviewMode) {
				openedPanels = this.getOpenedPanels.map((panel) => {
					return panel.id
				})
			}
		}
	},

	methods: {
		...mapActions([
			'setElementFocus',
			'copyElement',
			'deleteElement',
			'updateElementOptionValue',
			'closePanel',
			'openPanel',
			'savePage',
			'setCopiedElement',
			'setPreviewMode',
			'undo',
			'redo',
			'setCuttedElement',
			'addNotice',
			'togglePanel',
			'moveElement'
		]),
		debounceDelete: debounce(function (uid, parentUid) {
			const parentContent = this.getElementData(this.getElementFocus.parentUid).content

			const elementIndex = parentContent.indexOf(this.getElementFocus.uid)
			const previewElementUid = parentContent[elementIndex - 1]
			const nextElementUid = parentContent[elementIndex + 1]

			if (previewElementUid) {
				this.setElementFocus({
					uid: previewElementUid,
					parentUid: this.getElementFocus.parentUid,
					insertParent: this.getElementFocus.insertParent
				})
			} else if (nextElementUid) {
				this.setElementFocus({
					uid: nextElementUid,
					parentUid: this.getElementFocus.parentUid,
					insertParent: this.getElementFocus.insertParent
				})
			} else {
				this.setElementFocus(null)
			}
			this.deleteElement({
				elementUid: uid,
				parentUid: parentUid
			})
		}),
		debouncePaste: debounce(function (uid, parentUid, insertParent) {
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
		}),
		debounceDuplicate: debounce(function (uid, parentUid, insertParent) {
			this.copyElement({
				elementUid: uid,
				parentUid: parentUid,
				insertParent: insertParent
			})
			const parentContent = this.getElementData(parentUid).content
			const elementIndex = parentContent.indexOf(uid)
			const copiedElementUid = parentContent[elementIndex + 1]
			this.setElementFocus({
				uid: copiedElementUid,
				parentUid: parentUid,
				insertParent: insertParent
			})
		}),
		debounceUndo: debounce(function () {
			if (this.canUndo) {
				this.undo()
			}
		}),
		debounceRedo: debounce(function () {
			if (this.canRedo) {
				this.redo()
			}
		}),
		// end checkMousePosition
		applyShortcuts (e) {
			if (e.target.isContentEditable) {
				return
			}

			// Set preview mode
			if (e.which === 80 && e.ctrlKey) {
				this.setPreviewMode(!this.isPreviewMode)
				e.preventDefault()
			}

			// Keys bellow don't run in preview mode
			if (this.isPreviewMode) {
				return
			}

			// Shortcuts that needs an active element
			if (this.getElementFocus) {
				const activeElementFocus = this.getElementFocus

				// Duplicate - CTRL+D
				if (e.which === 68 && e.ctrlKey && !e.shiftKey) {
					this.debounceDuplicate(activeElementFocus.uid, activeElementFocus.parentUid, activeElementFocus.parentUid)

					e.preventDefault()
				}

				// copy
				if (e.which === 67 && e.ctrlKey && !e.shiftKey) {
					if (!e.target.getAttribute('contenteditable')) {
						const elementId = this.getElementData(activeElementFocus.uid).element_type
						this.setCopiedElement({
							uid: activeElementFocus.uid,
							isWrapper: getElement(elementId).wrapper
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
							isWrapper: getElement(elementId).wrapper
						})
						this.setCopiedElement(null)
					}
				}
				if (e.code === 'Escape' && this.getCuttedElement) {
					this.setCuttedElement(null)
				}

				if (e.which === 46) {
					if (!e.target.getAttribute('contenteditable')) {
						this.debounceDelete(activeElementFocus.uid, activeElementFocus.parentUid)
					}
				}

				// Copy element styles ctrl+shift+c
				if (e.ctrlKey && e.shiftKey && e.which === 67) {
					e.preventDefault()
					copiedStyles = activeElementFocus.elementData.options._styles
				}

				// Paste element styles ctrl + shift + v
				if (e.ctrlKey && e.shiftKey && e.which === 86) {
					const element = this.getElementData(activeElementFocus.uid)
					element.options.updateValues({
						...activeElementFocus.elementData.options,
						_styles: copiedStyles
					})
				}
			}

			// Undo CTRL+Z
			if (!this.isPanelElementOptionsOpen) {
				if (e.which === 90 && e.ctrlKey && !e.shiftKey) {
					this.debounceUndo()
				}
			}

			// Redo CTRL+SHIFT+D -- Back to WP Dashboard
			if (e.code === 'KeyD' && e.ctrlKey && e.shiftKey) {
				window.open(
					this.getEditPageUrl,
					'_blank'
				)
			}
			// Redo CTRL+SHIFT+Z CTRL + Y
			if (!this.isPanelElementOptionsOpen) {
				if ((e.which === 90 && e.ctrlKey && e.shiftKey) || (e.ctrlKey && e.which === 89)) {
					this.debounceRedo()
				}
			}

			// Save CTRL+S
			if (e.which === 83 && e.ctrlKey && !e.shiftKey) {
				e.preventDefault()
				if (!this.isDisplayingSaveNotice) {
					this.isDisplayingSaveNotice = true
					this.savePage({ status: 'publish' }).catch(error => {
						this.addNotice({
							message: error.message,
							type: 'error',
							delayClose: 5000
						})
					}).finally(() => {
						this.addNotice({
							message: this.$translate('success_save'),
							delayClose: 5000
						}).then(() => {
							this.isDisplayingSaveNotice = false
						})
					})
				}
			}

			// Hide element/panel
			if (e.which === 72 && e.ctrlKey) {
				if (this.getElementFocus) {
					let elementVisibility = this.getElementData(this.getElementFocus.uid).options._isVisible
					if (elementVisibility === undefined) {
						elementVisibility = true
					}
					this.updateElementOptionValue({
						elementUid: this.getElementFocus.uid,
						path: '_isVisible',
						newValue: !elementVisibility,
						type: 'visibility'
					})
					e.preventDefault()
				}
			}
			// Toggle treeView panel
			if (e.shiftKey && e.code === 'KeyT') {
				this.togglePanel('panel-tree')
				e.preventDefault()
			}
			// Opens Library
			if (e.shiftKey && e.code === 'KeyL') {
				this.togglePanel('PanelLibraryModal')
				e.preventDefault()
			}
			// Opens Page options
			if (e.shiftKey && e.code === 'KeyO') {
				this.togglePanel('panel-global-settings')
				e.preventDefault()
			}
		}
	}
}
