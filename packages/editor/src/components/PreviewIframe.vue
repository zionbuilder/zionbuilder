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
import { mapGetters, mapActions } from 'vuex'
import Cache from '../Cache.ts'
import Dom from '../dom.js'
import { flattenTemplateData } from '@zb/utils'
import { on, off } from '@zb/hooks'
import { each } from 'lodash-es'
import { useTemplateParts, usePreviewLoading, useElementFocus, useKeyBindings, useElements, useSavePage, useEditorData, useEditorInteractions } from '@data'
import { useResponsiveDevices } from '@zb/components'

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
		const { focusedElement, unFocusElement } = useElementFocus()
		const { applyShortcuts } = useKeyBindings()
		const { saveDraft } = useSavePage()
		const { page_id: pageId } = useEditorData()
		const { urls } = useEditorData()
		const { getIframePointerEvents, getIframeOrder } = useEditorInteractions()

		return {
			activeResponsiveDeviceInfo,
			focusedElement,
			unFocusElement,
			applyShortcuts,
			saveDraft,
			pageId,
			urls,
			getIframeOrder,
			getIframePointerEvents
		}
	},
	computed: {
		...mapGetters([
			'canUndo',
			'canRedo',
		]),
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
		...mapActions([
			'setPageAreas',
			'setPageContent',
			'setInitialHistory',
			'setActiveArea'
		]),
		useLocalVersion () {
			if (Object.keys(this.localStoragePageData).length) {
				this.setPageAreas(this.localStoragePageData.pageContent.contentRoot)
				this.setPageContent(this.localStoragePageData.pageContent)
				this.setActiveArea('content')
				this.setInitialHistory(this.$translate('initial_state'))
			}
			this.showRecoverModal = false
		},
		useServerVersion () {
			if (!this.ignoreNextReload) {
				const { contentWindow } = this.$refs.iframe

				if (!contentWindow.ZnPbPreviewData) {
					this.$zb.errors.add({
						message: this.$translate('page_content_error'),
						type: 'error',
						delayClose: 0
					})

					this.ignoreNextReload = false

					return false
				}

				// Set preview data
				const areaConfig = this.$refs.iframe.contentWindow.ZnPbPreviewData.page_content
				let pageContentElements = {}
				let pageContentAreas = {}

				const { registerTemplatePart, templateParts } = useTemplateParts()

				// New system
				each(areaConfig, (value, id) => {
					const area = registerTemplatePart({
						name: id,
						id: id
					})

					area.element.addChildren(value)
				})

				// TODO: implement history
				// this.setInitialHistory(this.$translate('initial_state'))

				// Hide recover modal
				this.showRecoverModal = false
			}
		},
		onIframeLoaded () {
			const { setPreviewLoading } = usePreviewLoading()
			this.iframeLoaded = true
			Dom.iframe = this.$refs.iframe
			Dom.iframeDocument = this.$refs.iframe.contentDocument
			Dom.iframeWindow = this.$refs.iframe.contentWindow

			this.attachIframeEvents()

			this.ignoreNextReload = false

			const cachedData = Cache.getItem(this.pageId)

			if (cachedData && Object.keys(cachedData).length > 0) {
				this.localStoragePageData = cachedData
				this.showRecoverModal = true
			} else {
				this.useServerVersion()
			}

			setPreviewLoading(false)
		},
		attachIframeEvents () {
			Dom.iframeDocument.addEventListener('click', this.deselectActiveElement, true)
			Dom.iframeDocument.addEventListener('click', this.preventClicks, true)
			Dom.iframeDocument.addEventListener('keydown', this.applyShortcuts)
			// Dom.iframeDocument.addEventListener('scroll', this.onScroll)
		},
		deselectActiveElement (event) {
			this.unFocusElement()
		},
		preventClicks (event) {
			const e = window.e || event

			if (e.target.tagName === 'A' && !e.target.classList.contains('znpb-allow-click')) {
				e.preventDefault()
			}
		},
		refreshIframe () {
			this.saveDraft().then(() => {
				this.ignoreNextReload = true
				Dom.iframeWindow.location.reload()
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
		if (Dom.iframeDocument) {
			Dom.iframeDocument.removeEventListener('click', this.deselectActiveElement)
			Dom.iframeDocument.removeEventListener('keydown', this.applyShortcuts)
			Dom.iframeDocument.removeEventListener('click', this.preventClicks, true)
		}

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
