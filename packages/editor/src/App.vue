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
								:class="{
									'znpb-has-responsive-options__icon-button--active': activeResponsiveDeviceId === device.id
								}"
								ref="dropdown"
							>
								<Icon :icon="device.icon" />
							</div>
						</template>
					</Tooltip>
				</div>
			</div>
		</transition>

		<div
			class="znpb-main-wrapper--mainBarPlaceholder"
			:class="{
				[`znpb-main-wrapper--mainBarPlaceholder--${mainBar.draggingPosition}`]: mainBar.isDragging && mainBar.draggingPosition
			}"
		/>

		<!-- top area -->
		<div class="znpb-top-area">
			<mainPanel v-if="mainBar.position === 'top'" />
		</div>

		<!-- center area -->
		<div class="znpb-center-area">
			<div
				id="znpb-panel-placeholder"
				v-if="panelPlaceholder.visibility"
				:style="{'left':panelPlaceholder.left + 'px'}"
			>
				<div class="znpb-panel-placeholder"></div>
			</div>

			<!-- start left area -->
			<mainPanel v-if="['left', 'right'].includes(mainBar.position)" />

			<!-- Start panels -->
			<template v-if="!isPreviewMode">
				<component
					v-for="panel in openPanels"
					:is="panel.id"
					:key="panel.id"
					:panel="panel"
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

		</div>
		<!-- end center area -->

		<!-- bottom area -->
		<div class="znpb-bottom-area">
			<mainPanel v-if="mainBar.position === 'bottom'" />
		</div>

		<!-- Add Elements Popup -->
		<AddElementPopup />
		<ElementMenu />
		<SaveElementModal />
		<PostLock />

		<!-- notices -->
		<Notice
			v-for="(error) in notifications"
			:key="error.message"
			@close-notice="error.remove()"
			:error="error"
		/>

	</div>
	<!-- end znpb-main-wrapper -->

</template>

<script>
import { provide, watch } from 'vue'

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
import SaveElementModal from './components/SaveElementModal.vue'

// Composables
import { AddElementPopup } from './components/AddElementPopup'
import { ElementMenu } from './components/ElementMenu'
import { usePanels, usePreviewMode, useKeyBindings, usePreviewLoading, useEditorInteractions, useEditorData, useAutosave } from '@composables'
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
		ElementMenu,
		SaveElementModal
	},
	setup (props) {
		const { fetchOptions } = useBuilderOptions()
		const { notifications } = useNotifications()
		const { openPanels, panelPlaceholder } = usePanels()
		const { activeResponsiveDeviceInfo, responsiveDevices, setActiveResponsiveDeviceId, activeResponsiveDeviceId } = useResponsiveDevices()
		const { isPreviewMode, setPreviewMode } = usePreviewMode()
		const { applyShortcuts } = useKeyBindings()
		const { isPreviewLoading } = usePreviewLoading()
		const { getMainbarPosition, mainBar } = useEditorInteractions()
		const { editorData } = useEditorData()

		// General functionality
		useAutosave()

		// Fetch the builder options
		fetchOptions()

		// Provide globalColors
		provide('builderOptions', useBuilderOptions)

		// provide masks for ShapeDividerComponent option
		provide('serverRequester', serverRequest)
		provide('masks', editorData.value.masks)
		provide('plugin_info', editorData.value.plugin_info)

		return {
			activeResponsiveDeviceId,
			notifications,
			panelPlaceholder,
			openPanels,
			activeResponsiveDeviceInfo,
			responsiveDevices,
			setActiveResponsiveDeviceId,
			isPreviewMode,
			setPreviewMode,
			applyShortcuts,
			isPreviewLoading,
			getMainbarPosition,
			mainBar,
			urls: editorData.value.urls
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
	},

	beforeUnmount: function () {
		// remove events
		document.removeEventListener('keydown', this.applyShortcuts)
	},
	mounted () {
		const { add } = useNotifications()

		document.addEventListener('keydown', this.applyShortcuts)

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
/* style default elements */
body {
	overflow: hidden;
}

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
			width: 36px;
			height: 36px;
			margin-bottom: 3px;
			color: var(--zb-surface-text-color);
			background: var(--zb-surface-color);
			box-shadow: 0 5px 10px 0 var(--zb-surface-shadow);
			border: 1px solid var(--zb-surface-lighter-color);
			border-radius: 50%;
			transition: all .15s;

			&:hover {
				color: var(--zb-surface-text-hover-color);
				background-color: var(--zb-surface-lighter-color);
			}

			.znpb-editor-icon-wrapper {
				font-size: 14px;
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

.znpb-has-responsive-options__icon-button--active {
	color: var(--zb-dropdown-text-active-color);
	background-color: var(--zb-dropdown-bg-active-color);
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
	flex: 1 1 auto;
	min-height: 1px;
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
	width: 6px;
	height: 100%;
	background-color: var(--zb-secondary-color);
	opacity: .5;
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
