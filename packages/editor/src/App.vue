<template>
	<div
		id="znpb-main-wrapper"
		class="znpb-main-wrapper"
	>
		<transition :name="showEditorTransition">
			<div
				:style="showEditorButtonStyle"
				class="znpb-editor-layout__preview-buttons"
				v-show="isPreviewMode"
			>
				<div
					class="znpb-editor-layout__preview-button"
					@click="showPanels"
				>
					<Icon icon="layout" />
				</div>
				<!-- devices -->
				<div
					@click="devicesVisible = !devicesVisible"
					class="znpb-editor-layout__preview-button"
				>

					<Icon :icon="activeResponsiveDeviceInfo.icon" />

				</div>
				<div
					class="znpb-editor-layout__preview-breakpoints"
					v-if="devicesVisible"
				>
					<!-- responsive option -->
					<Tooltip
						:show="devicesVisible"
						:show-arrows="false"
						append-to="element"
						:trigger="null"
						placement="bottom"
						:close-on-outside-click="true"
					>
						<!-- @show="openResponsive"
						@hide="closeresponsive" -->
						<template v-slot:content>
							<div
								v-for="(device, index) in responsiveDevices"
								:key='index'
								@click="activateDevice(device)"
								class="znpb-options-devices-buttons znpb-has-responsive-options__icon-button"
								ref="dropdown"
							>
								<Icon :icon="device.icon" />
							</div>
						</template>
					</Tooltip>
				</div>
			</div>
		</transition>
		<!-- top area -->
		<div class="znpb-top-area" />

		<!-- notices -->
		<Notice
			v-for="(error) in notifications"
			:key="error.message"
			@close-notice="error.remove()"
			:error="error"
		/>

		<!-- center area -->
		<div class="znpb-center-area">
			<div
				id="znpb-panel-placeholder"
				v-if="panelPlaceholder.visibility"
				:style="{'left':panelPlaceholder.left + 'px'}"
			>
				<div class="znpb-panel-placeholder"></div>
			</div>
			<transition
				:name="panelPreviewTransition"
				@after-leave="onAfterLeave"
				@after-enter="onAfterEnter"
			>
				<!-- start left area -->
				<mainPanel v-show="!isPreviewMode" />
			</transition>
			<!-- Start panels -->

			<template v-if="!isPreviewMode">
				<component
					v-for="panel in openPanels"
					:is="panel.id"
					:key="panel.id"
					:panel="panel"
					:show-move="false"
					@show-helper="showPlaceholderHelper=$event"
				/>
			</template>

			<!-- iframe wrapper area -->
			<PreviewIframe />
			<div
				class="znpb-loading-wrapper-gif"
				v-if="isPreviewLoading"
			>
				<img :src="urls.loader" />
				<div class="znpb-loading-wrapper-gif__text">{{$translate('generating_preview')}}</div>
			</div>

			<PostLock />

			<!-- right area -->
			<div class="znpb-right-area" />

		</div>
		<!-- end center area -->

		<!-- bottom area -->
		<div class="znpb-bottom-area " />

		<!-- Add Elements Popup -->
		<AddElementPopup />
		<ElementMenu />
	</div>
	<!-- end znpb-main-wrapper -->

</template>

<script>
import { provide } from 'vue'

// import components
import PanelLibraryModal from './components/PanelLibraryModal.vue'
import PanelTree from './components/treeView/PanelTree.vue'
import PanelGlobalSettings from './components/PanelGlobalSettings.vue'
import PanelHistory from './components/PanelHistory.vue'
import MainPanel from './components/main-panel.vue'
import PreviewIframe from './components/PreviewIframe.vue'
import PanelElementOptions from './components/PanelElementOptions.vue'
import PostLock from './components/PostLock.vue'
import DeviceElement from './components/DeviceElement.vue'
import { AddElementPopup } from './components/AddElementPopup'
import { ElementMenu } from './components/ElementMenu'
import { usePanels, usePreviewMode, useElementActions, useKeyBindings, usePreviewLoading, useEditorInteractions, useEditorData, useDemoMode } from '@composables'
import { useResponsiveDevices } from '@zb/components'
import { useNotifications, useBuilderOptions } from '@zionbuilder/composables'

import { serverRequest } from './api'

// WordPress hearbeat
require('./HeartBeat.js')

export default {
	name: 'znpb-editor-app',
	components: {
		PanelLibraryModal,
		PanelTree,
		PanelHistory,
		MainPanel,
		PreviewIframe,
		PanelGlobalSettings,
		PanelElementOptions,
		PostLock,
		DeviceElement,
		AddElementPopup,
		ElementMenu
	},
	setup (props) {
		const { isDemoMode } = useDemoMode()
		const { fetchOptions } = useBuilderOptions()
		const { notifications } = useNotifications()
		const { openPanels, panelPlaceholder } = usePanels()
		const { activeResponsiveDeviceInfo, responsiveDevices, setActiveResponsiveDeviceId } = useResponsiveDevices()
		const { isPreviewMode, setPreviewMode } = usePreviewMode()
		const { focusedElement, unFocusElement } = useElementActions()
		const { applyShortcuts } = useKeyBindings()
		const { isPreviewLoading } = usePreviewLoading()
		const { getMainbarPosition } = useEditorInteractions()
		const { editorData } = useEditorData()

		// Fetch the builder options
		fetchOptions()

		// Provide globalColors
		provide('builderOptions', useBuilderOptions)

		// provide masks for ShapeDividerComponent option
		provide('serverRequester', serverRequest)
		provide('masks', editorData.value.masks)
		provide('plugin_info', editorData.value.plugin_info)

		return {
			notifications,
			panelPlaceholder,
			openPanels,
			activeResponsiveDeviceInfo,
			responsiveDevices,
			setActiveResponsiveDeviceId,
			isPreviewMode,
			setPreviewMode,
			focusedElement,
			applyShortcuts,
			isPreviewLoading,
			getMainbarPosition,
			urls: editorData.value.urls,
			isDemoMode
		}
	},
	data: () => {
		return {
			devicesVisible: false
		}
	},
	computed: {
		showEditorTransition: function () {
			if (this.getMainbarPosition() === 'left') {
				return 'slide-from-right'
			}
			if (this.getMainbarPosition() === 'right') {
				return 'slide-from-left'
			}
			return 'slide-from-right'
		},
		panelPreviewTransition: function () {
			return `znpb-main-panel--out--position-${this.getMainbarPosition()}`
		},
		showEditorButtonStyle () {
			const mainBarPosition = this.getMainbarPosition()
			let buttonStyle
			if (mainBarPosition === 'right') {
				buttonStyle = {
					right: '30px',
					top: '30px;'
				}
			}
			if (mainBarPosition === 'left') {
				buttonStyle = {
					left: '30px',
					top: '30px'
				}
			}
			return buttonStyle
		}
	},
	methods: {
		activateDevice (device) {
			this.setActiveResponsiveDeviceId(device.id)
			setTimeout(() => {
				this.showDevices = false
			}, 50)
		},
		showPanels () {
			this.setPreviewMode(false)
		},
		showDevices () {
			this.devicesVisible = !this.devicesVisible
		},
		deselectActiveElement () {
			// Don't deselect the element if an element was just activated
			// TODO: implement this
			// if (!window.ZionBuilderApi.editor.ElementFocusMarshall.isHandled) {
			// 	if (this.focusedElement.value) {
			// 		this.unFocusElement()
			// 	}
		},
		onAfterLeave () {
			const el = document.querySelector('iframe')
			el.style.transform = 'translateZ(0)'
		},
		onAfterEnter () {
			const el = document.querySelector('iframe')
			el.style.transform = null
		}
	},

	beforeUnmount: function () {
		// remove events
		document.removeEventListener('click', this.deselectActiveElement)
		document.removeEventListener('keydown', this.applyShortcuts)
	},
	mounted () {
		const { add } = useNotifications()

		document.addEventListener('click', this.deselectActiveElement)
		document.addEventListener('keydown', this.applyShortcuts)

		// Check for demo mode
		if (this.isDemoMode) {
			document.body.classList.add('zbpb-editor-demoMode')
		}

		add({
			message: this.$translate('autosave_notice'),
			type: 'info',
			delayClose: 5000
		})
	}
	// end mounted
}
// end export default
</script>

<style lang="scss">
@import "./scss/index.scss";

/* style default elements */
.znpb-editor {
	&-layout {
		&__preview-buttons {
			position: fixed;
			z-index: 99999;
			cursor: pointer;
		}
		&__preview-button {
			display: flex;
			justify-content: center;
			align-items: center;
			width: 42px;
			height: 42px;
			margin-bottom: 3px;
			color: $font-color;
			background: $secondary-color--accent;
			box-shadow: 0 5px 10px 0 rgba(164, 164, 164, .15);
			border: 1px solid $surface-variant;
			border-radius: 50%;
			.znpb-editor-icon-wrapper {
				font-size: 16px;
			}
		}

		&__preview-breakpoints {
			display: flex;
			flex-direction: column;
			align-items: center;

			.hg-popper {
				padding: 8px 0;
			}

			.znpb-editor-icon-wrapper {
				display: block;
			}
		}

		&--full-view {
			.znpb-editor-header {
				flex-basis: 0;
				overflow: hidden;
				width: 0;
			}

			.znpb-editor-layout__show-editor-button {
				opacity: 1;
				visibility: visible;
			}
		}
	}

	&-wrapper {
		width: 100%;
		height: 100%;
	}
}
.znpb-left-area, .znpb-top-area, .znpb-bottom-area, .znpb-right-area {
	display: flex;
}

.znpb-left-area, .znpb-right-area {
	position: relative;
	height: auto;
	background: transparent;
}
.znpb-modal__confirm-buttons-wrapper.znpb-storage-recover-modal {
	.znpb-button {
		white-space: nowrap;
	}
}
.znpb-main-wrapper {
	overflow: hidden;
}

#znpb-html-app, #znpb-html-app body {
	width: 100%;
	height: 100%;
	margin: 0 !important;
}

.znpb-right-area {
	.znpb-editor-header {
		flex-direction: column;
	}
}

.znpb-center-area {
	display: flex;
	height: 100%;
}

.znpb-editor-header.main-panel-animation {
	margin-right: auto;
	margin-left: auto;

	&.znpb-editor-header--right {
		margin-right: -60px;
	}
}
/*  Transitions */
/*  slide from right */
.slide-from-right-enter-to {
	transform: translateX(0);
	transition: all .3s ease-out;
	transition-delay: .1s;
}

.slide-from-right-leave-from {
	transition: all .3s ease-in;
}
.slide-from-right-enter-from, .slide-from-right-leave-to {
	transform: translateX(60px);
	opacity: 0;
}
/*  slide from left */
.slide-from-left-enter-to {
	transform: translateX(0);
	transition: all .3s ease-out;
	transition-delay: .1s;
}

.slide-from-left-leave-from {
	transition: all .3s ease-in;
}
.slide-from-left-enter-from, .slide-from-left-leave-to {
	transform: translateX(-60px);
	opacity: 0;
}

.znpb-main-panel {
	&--out {
		&--position-left {
			&-enter, &-leave-to {
				margin-left: -60px;
			}
		}

		&--position-right {
			&-enter, &-leave-to {
				margin-right: -60px;
			}
		}
	}
}

/* General styles */
body {
	padding: 0;
	margin: 0;
}

.znpb-loading-wrapper-gif {
	position: absolute;
	top: 0;
	left: 0;
	z-index: 999999;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	order: 2;
	width: 100%;
	width: 100%;
	height: 100%;
	background: #fff;

	img {
		margin-bottom: 30px;
		opacity: .6;
	}

	&__text {
		color: #000;
		font-size: 16px;
		font-weight: 300;
	}
}
#znpb-panel-placeholder {
	position: absolute;
	width: 5px;
	height: 100%;
	background-color: rgba($secondary, .6);
}
.znpb-panel-placeholder {
	width: 100%;
	height: 100%;
}
.zbpb-editor-demoMode {
	.znpb-editor-header__page-save-wrapper--save, .znpb-editor-header__page-save-wrapper--save a, .znpb-editor-header__menu-list li:nth-child(4), .znpb-editor-header__menu-list li:nth-child(5), .znpb-editor-header__menu-list li:nth-child(6) {
		cursor: not-allowed !important;
	}

	.znpb-editor-header__page-save-wrapper--save
	.znpb-editor-header__menu_button, .znpb-editor-header__page-save-wrapper--save a, .znpb-editor-header__menu-list li:nth-child(4) a, .znpb-editor-header__menu-list li:nth-child(5) a, .znpb-editor-header__menu-list li:nth-child(6) a {
		pointer-events: none !important;
	}

	// Save element modal
	.znpb-modal-save-element {
		.znpb-modal-content-save-buttons {
			cursor: not-allowed !important;
			& > div {
				pointer-events: none !important;
			}
		}
	}
}
</style>
