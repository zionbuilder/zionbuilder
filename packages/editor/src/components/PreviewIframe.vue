<template>
	<div
		class="znpb-editor-iframe-wrapper"
		:style="pointerevents"
		:class="getWrapperClasses"
		id="preview-iframe"
		ref="root"
	>

		<iframe
			v-if="urls.preview_frame_url"
			ref="iframe"
			@load="checkIframeLoading"
			id="znpb-editor-iframe"
			:src="urls.preview_frame_url"
			:style="deviceStyle"
		/>

	</div>
</template>

<script>
import { ref, render } from 'vue'
import { on, off } from '@zb/hooks'
import { each } from 'lodash-es'
import {
	useTemplateParts,
	usePreviewLoading,
	useKeyBindings,
	useSavePage,
	useEditorData,
	useUI,
	useWindows,
	useHistory,
	useElementTypes
} from '@composables'
import { useResponsiveDevices } from '@zb/components'
import { useNotifications } from '@zionbuilder/composables'

export default {
	name: 'preview-iframe',
	data () {
		return {
			ignoreNextReload: false,
			showRecoverModal: false,
			localStoragePageData: {},
			iframeLoaded: false
		}
	},
	setup () {
		const { activeResponsiveDeviceInfo } = useResponsiveDevices()
		const { applyShortcuts } = useKeyBindings()
		const { saveAutosave } = useSavePage()
		const { editorData } = useEditorData()
		const { isDirty } = useHistory()
		const { getIframePointerEvents, getPanelOrder } = useUI()
		const { addWindow, addEventListener, removeEventListener, getWindows, removeWindow } = useWindows()

		const root = ref(null)

		return {
			activeResponsiveDeviceInfo,
			applyShortcuts,
			saveAutosave,
			pageId: editorData.value.page_id,
			urls: editorData.value.urls,
			getPanelOrder,
			getIframePointerEvents,
			addWindow,
			getWindows,
			addEventListener,
			removeEventListener,
			removeWindow,
			root,
			isDirty
		}
	},
	computed: {
		deviceStyle: function () {
			let style = {}
			if (this.activeResponsiveDeviceInfo) {
				if (this.activeResponsiveDeviceInfo.isLandscape) {
					style.width = this.activeResponsiveDeviceInfo.height.value + this.activeResponsiveDeviceInfo.height.unit
					style.height = this.activeResponsiveDeviceInfo.width.value + this.activeResponsiveDeviceInfo.width.unit
				} else {
					style.width = this.activeResponsiveDeviceInfo.width.value + this.activeResponsiveDeviceInfo.width.unit
					style.height = this.activeResponsiveDeviceInfo.height.value + this.activeResponsiveDeviceInfo.height.unit
				}
			}

			return style
		},
		pointerevents: function () {
			let style = {}

			if (this.getIframePointerEvents()) {
				style.pointerEvents = 'none'
			}
			style.order = this.getPanelOrder('preview-iframe')
			return style
		},
		getWrapperClasses () {
			return `znpb-editor-iframe-wrapper--${this.activeResponsiveDeviceInfo.id}`
		}
	},
	methods: {
		setPageContent (areas) {
			const { registerTemplatePart } = useTemplateParts()
			const { setContentTimestamp } = usePreviewLoading()

			// New system
			each(areas, (value, id) => {
				const area = registerTemplatePart({
					name: id,
					id: id
				})

				area.element.addChildren(value)
			})

			// Add timestamp for content
			setContentTimestamp()

			// Add to history
			const { addInitialHistory } = useHistory()
			addInitialHistory()
		},

		useServerVersion () {
			if (!this.ignoreNextReload) {
				const { contentWindow } = this.$refs.iframe

				this.setPageContent(contentWindow.ZnPbPreviewData.page_content)

				// Hide recover modal
				this.showRecoverModal = false
			}
		},
		onIframeClick (event) {
			this.root.click()
		},
		onIframeLoaded () {
			const { add } = useNotifications()
			const { addElementTypes } = useElementTypes()
			const { setPreviewLoading, setLoadTimestamp } = usePreviewLoading()

			this.iframeLoaded = true
			const iframeWindow = this.$refs.iframe.contentWindow

			// Save the timestamp so we can use if in various ways. One example is the tree view justAdded element feature
			setLoadTimestamp()

			// Register the document
			this.addWindow('preview', iframeWindow)
			this.attachIframeEvents()

			if (!iframeWindow.ZnPbPreviewData) {
				add({
					message: this.$translate('page_content_error'),
					type: 'error',
					delayClose: 0
				})

				return false
			}

			// Set preview data
			if (!this.ignoreNextReload) {
				addElementTypes(iframeWindow.ZnPbPreviewData.elements_data)
			}


			this.useServerVersion()
			this.ignoreNextReload = false

			setPreviewLoading(false)
		},
		attachIframeEvents () {
			this.getWindows('preview').addEventListener('click', this.preventClicks, true)
			this.getWindows('preview').addEventListener('keydown', this.applyShortcuts)
			this.getWindows('preview').addEventListener('beforeunload', this.onBeforeUnloadIframe, { capture: true })
			this.getWindows('preview').addEventListener('click', this.onIframeClick, true)
		},
		preventClicks (event) {
			const e = window.e || event

			if (e.target.tagName === 'a' || !e.target.classList.contains('znpb-allow-click')) {
				e.preventDefault()
			}
		},
		onBeforeUnloadIframe (event) {
			if (this.isDirty) {
				event.preventDefault()
				event.returnValue = 'Do you want to leave this site? Changes you made may not be saved.'
			} else {
				const { setPreviewLoading } = usePreviewLoading()

				setPreviewLoading(true)
			}
		},
		refreshIframe () {
			this.saveAutosave().then(() => {
				this.ignoreNextReload = true
				this.getWindows('preview').location.reload(true)
				this.removeWindow('preview')
			})
		},
		checkIframeLoading () {
			if (this.$refs.iframe && this.$refs.iframe.contentDocument) {
				if (this.$refs.iframe.contentDocument.readyState === 'complete') {
					this.onIframeLoaded()
				} else {
					setTimeout(this.checkIframeLoading, 100)
				}
			} else {
				setTimeout(this.checkIframeLoading, 100)
			}
		}
	},
	// end checkMousePosition
	beforeUnmount () {
		this.getWindows('preview').removeEventListener('keydown', this.applyShortcuts)
		this.getWindows('preview').removeEventListener('click', this.preventClicks, true)
		this.getWindows('preview').removeEventListener('beforeunload', this.onBeforeUnloadIframe)
		this.getWindows('preview').removeEventListener('click', this.onIframeClick, true)

		off('refreshIframe', this.refreshIframe)
	},
	mounted () {
		on('refreshIframe', this.refreshIframe)
	}
}
</script>

<style lang="scss">
.znpb-editor-iframe-wrapper {
	display: flex;
	justify-content: center;
	align-items: center;
	order: 3;
	flex: 1 1 0;
	background-color: var(--zb-surface-darker-color);
}

// Iframe
#znpb-editor-iframe {
	max-width: 100%;
	min-height: 150px;
	max-height: 100%;
	box-shadow: 0 0 60px 0 rgba(0, 0, 0, .1);
	border: 0;
	transition: all .5s;
}
.znpb-editor-iframe-wrapper:not(.znpb-editor-iframe-wrapper--default)
	#znpb-editor-iframe {
	max-height: calc(100vh - 60px);
}
</style>
