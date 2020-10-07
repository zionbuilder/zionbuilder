<template>
	<div
		class="znpb-editor-iframe-wrapper"
		:style="pointerevents"
		:class="getWrapperClasses"
	>

		<iframe
			v-if="getPreviewFrameUrl"
			ref="iframe"
			@load="checkIframeLoading"
			id="znpb-editor-iframe"
			:src="getPreviewFrameUrl"
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
import keyBindingsMixin from '../mixins/keyBindingsMixin.js'
import Cache from '../Cache.ts'
import Dom from '../dom.js'
import { flattenTemplateData } from '@zb/utils'
import { on, off } from '@zb/hooks'
import { PageElement } from '../data/models/PageData'

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
	mixins: [keyBindingsMixin],
	computed: {
		...mapGetters([
			'getActiveDevice',
			'getPreviewFrameUrl',
			'getIframePointerEvents',
			'canUndo',
			'canRedo',
			'getRightClickMenu',
			'getIframeOrder',
			'getPageId',
			'getElementFocus'
		]),
		storageRecover () {
			return this.localStoragePageData && this.showRecoverModal
		},
		deviceStyle: function () {
			let style = {}
			if (this.getActiveDevice) {
				if (this.getActiveDevice.isLandscape) {
					style.width = this.getActiveDevice.height.value + this.getActiveDevice.height.unit
					style.height = this.getActiveDevice.width.value + this.getActiveDevice.width.unit
				} else {
					style.width = this.getActiveDevice.width.value + this.getActiveDevice.width.unit
					style.height = this.getActiveDevice.height.value + this.getActiveDevice.height.unit
				}
			}

			return style
		},
		pointerevents: function () {
			let style = {}

			if (this.getIframePointerEvents) {
				style.pointerEvents = 'none'
			}
			style.order = this.getIframeOrder
			return style
		},
		getWrapperClasses () {
			return `znpb-editor-iframe-wrapper--${this.getActiveDevice.id}`
		}
	},
	methods: {
		...mapActions([
			'setPreviewFrameLoading',
			'setPageAreas',
			'setPageContent',
			'setInitialHistory',
			'setActiveArea',
			'setElementFocus',
			'setRightClickMenu',
			'addNotice'
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
					this.addNotice({
						message: this.$translate('page_content_error'),
						type: 'error',
						delayClose: 0
					})

					this.setPreviewFrameLoading(false)
					this.ignoreNextReload = false

					return false
				}

				// Set preview data
				const areaConfig = this.$refs.iframe.contentWindow.ZnPbPreviewData.page_content
				let pageContentElements = {}
				let pageContentAreas = {}

				Object.keys(areaConfig).forEach(areaId => {
					const areaContent = areaConfig[areaId]
					let flattenData = flattenTemplateData(areaContent)
					pageContentElements = { ...flattenData, ...pageContentElements }
					pageContentAreas[areaId] = []

					if (Array.isArray(areaContent)) {
						areaContent.forEach(elementConfig => {
							pageContentAreas[areaId].push(elementConfig.uid)
						})
					}
				})

				pageContentElements = {
					...pageContentElements,
					contentRoot: {
						element_type: 'root',
						content: pageContentAreas.content,
						options: {},
						uid: 'contentRoot'
					}
				}

				const elementsWithInstances = {}
				Object.keys(pageContentElements).forEach(uid => {
					const elementConfig = pageContentElements[uid]
					elementsWithInstances[uid] = new PageElement(elementConfig)
				});

				this.setPageAreas(pageContentAreas)
				this.setPageContent(elementsWithInstances)
				this.setActiveArea('content')
				this.setInitialHistory(this.$translate('initial_state'))

				// Hide recover modal
				this.showRecoverModal = false
			}
		},
		onIframeLoaded () {
			this.iframeLoaded = true
			Dom.iframe = this.$refs.iframe
			Dom.iframeDocument = this.$refs.iframe.contentDocument
			Dom.iframeWindow = this.$refs.iframe.contentWindow

			this.attachIframeEvents()
			this.setPreviewFrameLoading(false)
			this.ignoreNextReload = false

			const cachedData = Cache.getItem(this.getPageId)

			if (cachedData && Object.keys(cachedData).length > 0) {
				this.localStoragePageData = cachedData
				this.showRecoverModal = true
			} else {
				this.useServerVersion()
			}
		},
		attachIframeEvents () {
			Dom.iframeDocument.addEventListener('click', this.deselectActiveElement)
			Dom.iframeDocument.addEventListener('click', this.preventClicks, true)
			Dom.iframeDocument.addEventListener('keydown', this.applyShortcuts)
			// Dom.iframeDocument.addEventListener('scroll', this.onScroll)
		},
		deselectActiveElement (event) {
			// Don't deselect the element if an element was just activated
			if (!window.ZionBuilderApi.editor.ElementFocusMarshall.isHandled) {
				if (this.getElementFocus) {
					this.setElementFocus(null)
				}

				if (this.getRightClickMenu && this.getRightClickMenu.visibility) {
					this.setRightClickMenu({
						visibility: false
					})
				}
			}
		},
		preventClicks (event) {
			const e = window.e || event

			if (e.target.tagName === 'A' && !e.target.classList.contains('znpb-allow-click')) {
				e.preventDefault()
			}
		},
		refreshIframe () {
			this.savePage({ status: 'autosave' }).then(() => {
				this.ignoreNextReload = true
				Dom.iframeWindow.location.reload()
			})
		},
		onScroll (e) {
			this.setRightClickMenu({
				visibility: false
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
	watch: {
		getRightClickMenu (newValue) {
			if (newValue && newValue.visibility) {
				this.$refs.iframe.contentDocument.addEventListener('scroll', this.onScroll)
			} else {
				this.$refs.iframe.contentDocument.removeEventListener('scroll', this.onScroll)
			}
		}
	},
	// end checkMousePosition
	beforeUnmount () {
		if (Dom.iframeDocument) {
			Dom.iframeDocument.removeEventListener('click', this.deselectActiveElement)
			Dom.iframeDocument.removeEventListener('keydown', this.applyShortcuts)
			Dom.iframeDocument.removeEventListener('click', this.preventClicks, true)
			Dom.iframeDocument.removeEventListener('scroll', this.onScroll)
		}

		off('refreshIframe', this.refreshIframe)
		this.$refs.iframe.contentDocument.removeEventListener('scroll', this.onScroll)
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
