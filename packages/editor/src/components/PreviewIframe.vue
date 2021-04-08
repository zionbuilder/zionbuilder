<template>
	<div
		class="znpb-editor-iframe-wrapper"
		:style="pointerevents"
		:class="getWrapperClasses"
	>

		<iframe
			v-if="urls.preview_frame_url"
			ref="iframe"
			@load="checkIframeLoading"
			id="znpb-editor-iframe"
			:src="urls.preview_frame_url"
			:style="deviceStyle"
		/>

		<Modal
			:show-close="false"
			:show-maximize="false"
			:show="storageRecover"
			append-to="body"
			:width="520"
		>
			<div class="znpb-modal__confirm">
				<!-- @slot Content that will be placed inside the modal body -->
				<p> {{$translate('storage_data_available')}} </p>
			</div>

			<div class="znpb-modal__confirm-buttons-wrapper znpb-storage-recover-modal">
				<Button
					type="secondary"
					v-if="true"
					@click="useLocalVersion"
				> {{$translate('use_local_version')}} </Button>
				<Button
					type="line"
					v-if="true"
					@click="useServerVersion"
				> {{$translate('use_server_version')}} </Button>
			</div>
		</Modal>
	</div>
</template>

<script>
import Cache from '../Cache.ts'
import { on, off } from '@zb/hooks'
import { each } from 'lodash-es'
import {
	useTemplateParts,
	usePreviewLoading,
	useElementActions,
	useKeyBindings,
	useSavePage,
	useEditorData,
	useEditorInteractions,
	useWindows,
	useHistory,
	usePageSettings,
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
		const { focusedElement, unFocusElement } = useElementActions()
		const { applyShortcuts } = useKeyBindings()
		const { saveAutosave } = useSavePage()
		const { editorData } = useEditorData()
		const { getIframePointerEvents, getIframeOrder } = useEditorInteractions()
		const { addWindow, addEventListener, removeEventListener, getWindows, removeWindow } = useWindows()

		return {
			activeResponsiveDeviceInfo,
			focusedElement,
			unFocusElement,
			applyShortcuts,
			saveAutosave,
			pageId: editorData.value.page_id,
			urls: editorData.value.urls,
			getIframeOrder,
			getIframePointerEvents,
			addWindow,
			getWindows,
			addEventListener,
			removeEventListener,
			removeWindow
		}
	},
	computed: {
		storageRecover () {
			return this.localStoragePageData && this.showRecoverModal
		},
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
			style.order = this.getIframeOrder()
			return style
		},
		getWrapperClasses () {
			return `znpb-editor-iframe-wrapper--${this.activeResponsiveDeviceInfo.id}`
		}
	},
	methods: {
		setPageContent (areas) {
			const { registerTemplatePart } = useTemplateParts()

			// New system
			each(areas, (value, id) => {
				const area = registerTemplatePart({
					name: id,
					id: id
				})

				area.element.addChildren(value)
			})

			// Add to history
			const { addInitialHistory } = useHistory()
			addInitialHistory()
		},
		useLocalVersion () {
			const content = {
				content: this.localStoragePageData.template_data || []
			}
			this.setPageContent(content)

			this.showRecoverModal = false
		},
		useServerVersion () {
			if (!this.ignoreNextReload) {
				const { contentWindow } = this.$refs.iframe

				this.setPageContent(contentWindow.ZnPbPreviewData.page_content)

				// Hide recover modal
				this.showRecoverModal = false
			}
		},
		onIframeLoaded () {
			const { add } = useNotifications()
			const { addElementTypes } = useElementTypes()
			const { setPreviewLoading } = usePreviewLoading()

			this.iframeLoaded = true
			const iframeWindow = this.$refs.iframe.contentWindow

			// Register the document
			this.addWindow('preview', iframeWindow)
			this.attachIframeEvents()

			const cachedData = Cache.getItem(this.pageId)

			if (!iframeWindow.ZnPbPreviewData) {
				add({
					message: this.$translate('page_content_error'),
					type: 'error',
					delayClose: 0
				})

				this.ignoreNextReload = false

				return false
			}

			// Set preview data
			addElementTypes(iframeWindow.ZnPbPreviewData.elements_data)

			if (cachedData && Object.keys(cachedData).length > 0) {
				this.localStoragePageData = cachedData
				this.showRecoverModal = true
			} else {
				this.useServerVersion()
			}

			setPreviewLoading(false)
		},
		attachIframeEvents () {
			this.getWindows('preview').addEventListener('click', this.deselectActiveElement, true)
			this.getWindows('preview').addEventListener('click', this.preventClicks, true)
			this.getWindows('preview').addEventListener('keydown', this.applyShortcuts)
			this.getWindows('preview').addEventListener('beforeunload', this.onBeforeUnloadIframe)
			// this.addEventListener('scroll', this.onScroll)
		},
		deselectActiveElement (event) {
			this.unFocusElement()
		},
		preventClicks (event) {
			const e = window.e || event

			if (e.target.tagName === 'a' || !e.target.classList.contains('znpb-allow-click')) {
				e.preventDefault()
			}
		},
		onBeforeUnloadIframe () {
			const { setPreviewLoading } = usePreviewLoading()
			const { unsetPageSettings } = usePageSettings()

			setPreviewLoading(true)
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
		this.getWindows('preview').removeEventListener('click', this.deselectActiveElement)
		this.getWindows('preview').removeEventListener('keydown', this.applyShortcuts)
		this.getWindows('preview').removeEventListener('click', this.preventClicks, true)

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
	background-color: #f1f1f1;
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
