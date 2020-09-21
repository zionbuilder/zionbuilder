// define a mixin object
import { mapGetters, mapActions } from 'vuex'
import { getOptionValue } from '@zb/utils'
import { getElement } from '@zb/editor/elements'

export default {
	props: {
		elementUid: {
			type: String,
			required: true
		},
		parentUid: {
			type: String,
			required: false
		}
	},
	data: function () {
		return {
			isNameChangeActive: false
		}
	},
	computed: {
		...mapGetters([
			'getElementData'
		]),
		elementTemplateData () {
			return this.getElementData(this.elementUid) || {}
		},
		elementModel () {
			const elementId = this.getElementData(this.elementUid).element_type

			return getElement(elementId)
		},
		elementTypeName () {
			return this.elementModel.name
		},
		elementName () {
			const elementSavedName = getOptionValue(this.elementTemplateData.options, '_advanced_options._element_name')
			return elementSavedName || this.elementTypeName
		},
		isElementVisible () {
			return this.getElementData(this.elementUid).options._isVisible !== false
		},
		templateItems () {
			return this.elementTemplateData.content || []
		},
		options () {
			return this.elementTemplateData.options || {}
		},
		elementCssId () {
			return (this.options._advanced_options || {})._element_id || this.elementTemplateData.uid
		}
	},
	methods: {
		...mapActions([
			'renameElement',
			'updateElementOptionValue',
			'setActiveElement',
			'openPanel'
		]),
		doRenameElement (newName) {
			this.renameElement({
				elementUid: this.elementUid,
				elementName: newName
			})

			this.isNameChangeActive = false
		},
		makeElementVisible () {
			this.updateElementOptionValue({
				elementUid: this.elementUid,
				path: '_isVisible',
				newValue: true,
				type: 'visibility'
			})
		},
		editElement () {
			this.setActiveElement(this.elementUid)
			this.openPanel('PanelElementOptions')
		},
		highlightElement () {
			const domElement = window.frames['znpb-editor-iframe'].contentDocument.getElementById(this.elementCssId)
			if (domElement) {
				domElement.classList.add('znpb-element__wrapper--panel-hovered')
				this.hovered = true
			}
		},
		unHighlightElement () {
			const domElement = window.frames['znpb-editor-iframe'].contentDocument.getElementById(this.elementCssId)
			if (domElement) {
				domElement.classList.remove('znpb-element__wrapper--panel-hovered')
				this.hovered = false
			}
		}
	}
}
