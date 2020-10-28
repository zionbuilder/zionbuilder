import { mapGetters, mapActions } from 'vuex'
import { debounce } from 'lodash-es'
import { usePanels, usePreviewMode, useElementFocus } from '@data'

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
			'getElementData',
			'canUndo',
			'canRedo',
			'getCopiedElement',
			'getCuttedElement',
			'getEditPageUrl'
		])
	},
	setup () {
		const { openPanel, closePanel, openPanels, togglePanel } = usePanels()
		const { isPreviewMode } = usePreviewMode()
		const { focusedElement } = useElementFocus()

		const debounceDelete = debounce(function (uid, parentUid) {
			const parentContent = focusedElement.value.parent.content
			const elementIndex = parentContent.indexOf(focusedElement.value.uid)
			const previewElementUid = parentContent[elementIndex - 1]
			const nextElementUid = parentContent[elementIndex + 1]

			if (previewElementUid) {
				this.setElementFocus({
					uid: previewElementUid,
					parentUid: this.focusedElement.value.parentUid,
					insertParent: this.focusedElement.value.insertParent
				})
			} else if (nextElementUid) {
				this.setElementFocus({
					uid: nextElementUid,
					parentUid: this.focusedElement.value.parentUid,
					insertParent: this.focusedElement.value.insertParent
				})
			} else {
				this.setElementFocus(null)
			}
			this.deleteElement({
				elementUid: uid,
				parentUid: parentUid
			})
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

		const debounceDuplicate = debounce(function (uid, parentUid, insertParent) {
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

		return {
			openPanel,
			closePanel,
			openPanels,
			togglePanel,
			isPreviewMode,
			focusedElement
		}
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

			if (!this.isPreviewMode.value) {
				openedPanels = this.openPanels.map((panel) => {
					return panel.id
				})
			}
		}
	}
}
