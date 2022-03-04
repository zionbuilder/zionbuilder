<template>
	<div
		class="znpb-editor-iframe-wrapper"
		:style="pointerevents"
		:class="getWrapperClasses"
		id="preview-iframe"
		ref="root"
	>
		<div class="znpb-editor-iframe-wrapperContainer"
				:style="deviceStyle">
			<iframe
				v-if="urls.preview_frame_url"
				ref="iframe"
				@load="checkIframeLoading"
				id="znpb-editor-iframe"
				:src="urls.preview_frame_url"
				:style="iframeStyles"
			/>
		</div>

		<div class="znpb-editor-iframeWidthTooltip" >
			{{iframeWidth}}px
		</div>

		<div class="znpb-editor-iframeWidthTooltip" v-if="showWidthTooltip">
			{{iframeWidth}}px
		</div>

	</div>
</template>

<script>
import { ref, computed } from 'vue'
import { on, off } from '@zb/hooks'
import { each } from 'lodash-es'
import rafSchd from 'raf-schd'
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
		const { activeResponsiveDeviceInfo, iframeWidth, setCustomIframeWidth, scaleValue, autoscaleActive } = useResponsiveDevices()
		const { applyShortcuts } = useKeyBindings()
		const { saveAutosave } = useSavePage()
		const { editorData } = useEditorData()
		const { isDirty } = useHistory()
		const { getIframePointerEvents, getPanelOrder } = useUI()
		const { addWindow, addEventListener, removeEventListener, getWindows, removeWindow } = useWindows()

		// Dom refs
		const root = ref(null)
		const iframe = ref(null)

		const getWrapperClasses = computed(() => {
			return {
				[`znpb-editor-iframe-wrapper--${activeResponsiveDeviceInfo.value.id}`]: true,
				'znpb-editor-iframe--isAutoscale': autoscaleActive.value,
			}
		})

		const deviceStyle = computed(() => {
			let styles = {}
			if (activeResponsiveDeviceInfo.value && activeResponsiveDeviceInfo.value.height) {
				styles.height = activeResponsiveDeviceInfo.value.height.value + this.activeResponsiveDeviceInfo.value.height.unit
			}

			if (!autoscaleActive.value) {
				if (iframeWidth.value) {
					styles.width = `${iframeWidth.value}px`
				}
			}

			return styles
		})

		const iframeStyles = computed(() => {
			const styles = {}

			if (root.value) {
				// CHeck if scale is auto
				if (autoscaleActive.value === true) {
					const { width: containerWidth, height: containerHeight } = root.value.getBoundingClientRect()
					const { width: iframeWidth, height: iframeHeight } = iframe.value.getBoundingClientRect()

					const scale = iframeWidth / containerWidth

					styles.transform = `scale(${scale})`

				} else if (scaleValue.value !== 100) {
					styles.transform = `scale(${scaleValue.value/100})`
				}
			}

			return styles
		})

		// Iframe size tooltip
		const showWidthTooltip = ref(false)
		let tooltipTimeout = null

		function onIframeResize( event ) {
			showWidthTooltip.value = true
			iframeWidth.value = event.target.document.body.clientWidth

			clearTimeout(tooltipTimeout)
			tooltipTimeout = setTimeout(() => {
				showWidthTooltip.value = false
			}, 500);
		}

		const onIframeResizeRaf = rafSchd(onIframeResize, 50)

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

			// Dom refs
			root,
			iframe,
			isDirty,

			// computed
			deviceStyle,
			getWrapperClasses,
			iframeStyles,

			// Iframe size tooltip
			onIframeResizeRaf,
			showWidthTooltip,
			iframeWidth,
			setCustomIframeWidth
		}
	},
	computed: {
		pointerevents: function () {
			let style = {}

			if (this.getIframePointerEvents()) {
				style.pointerEvents = 'none'
			}
			style.order = this.getPanelOrder('preview-iframe')
			return style
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

			// Show width tooltip


			// Set iframe width
			this.setCustomIframeWidth(iframeWindow.document.body.clientWidth)
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
		this.getWindows('preview').removeEventListener('resize', this.onIframeResizeRaf)

		off('refreshIframe', this.refreshIframe)
	},
	mounted () {
		on('refreshIframe', this.refreshIframe)
	}
}
</script>

<style lang="scss">
.znpb-editor-iframe-wrapper {
	position: relative;
	display: flex;
	flex-direction: column;
	justify-content: center;
	order: 3;
	flex: 1 1 0;
	overflow-x: auto;
	overflow-y: hidden;
	background-color: var(--zb-surface-darker-color);
}

.znpb-editor-iframe-wrapperContainer {
	position: relative;
	width: 100%;
	height: 100%;
	margin: 0 auto;
	transition: all .5s;
}

// Device specific
.znpb-editor-iframe-wrapper--default {
	#znpb-editor-iframe {
		min-width: 1024px;
	}
}

.znpb-editor-iframe--isAutoscale {
	overflow: hidden;
}

.znpb-editor-iframe--isAutoscale #znpb-editor-iframe {
	transform-origin: 0 0 0;
}

// Iframe
#znpb-editor-iframe {
	width: 100%;
	height: 100%;
	box-shadow: 0 0 60px 0 rgba(0, 0, 0, .1);
	border: 0;
}

.znpb-editor-iframe-wrapper:not(.znpb-editor-iframe-wrapper--default) #znpb-editor-iframe {
	max-height: calc(100vh - 60px);
}

// Size tooltip
.znpb-editor-iframeWidthTooltip {
	position: absolute;
	right: 30px;
	bottom: 20px;
	padding: 8px 4px;
	background: #fff;
	border-radius: 3px;
}
</style>
