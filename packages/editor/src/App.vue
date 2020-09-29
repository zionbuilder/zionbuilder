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
					v-on:click="showPanels"
				>
					<BaseIcon icon="layout" />
				</div>
				<!-- devices -->
				<div
					@click="devicesVisible = !devicesVisible"
					class="znpb-editor-layout__preview-button"
				>
					<BaseIcon
						slot="panel-icon"
						:icon="device"
					>
					</BaseIcon>
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
						<div
							slot="content"
							v-for="(device, index) in getDeviceList"
							:key='index'
							@click="activateDevice(device)"
							class="znpb-options-devices-buttons znpb-has-responsive-options__icon-button"
							ref="dropdown"
						>
							<BaseIcon :icon="device.icon" />
						</div>
					</Tooltip>
				</div>
			</div>
		</transition>
		<!-- top area -->
		<div class="znpb-top-area" />

		<!-- notices -->
		<Notice
			v-for="(error) in getErrors"
			:key="error.message"
			@close-notice="removeNotice(error)"
			:error="error"
		/>

		<!-- center area -->
		<div class="znpb-center-area">
			<div
				id="znpb-panel-placeholder"
				v-if="getPanelPlaceholder.visibility"
				:style="{'left':getPanelPlaceholder.left + 'px'}"
			>
				<div class="
				znpb-panel-placeholder"></div>
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
			<component
				v-for="panelConfig in openPanels"
				:is="panelConfig.id"
				:key="panelConfig.id"
				:show-move="false"
				:data="rootData"
				@show-helper="showPlaceholderHelper=$event"
			/>

			<!-- iframe wrapper area -->
			<PreviewIframe />
			<div
				class="znpb-loading-wrapper-gif"
				v-if="getStylesLoading && !loadedMainApp"
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

		<RightClickMenu
			v-if="rightClickVisibility"
			:show-rename="rightClickSource!=='preview'"
		/>
	</div>
	<!-- end znpb-main-wrapper -->

</template>

<script>
// import RightClickMenu from '@/preview/components/RightClickMenu'
// import components
import PanelLibraryModal from './components/PanelLibraryModal.vue'
import PanelTree from './components/treeView/PanelTree.vue'
import PanelGlobalSettings from './components/PanelGlobalSettings.vue'
import PanelHistory from './components/PanelHistory.vue'
import MainPanel from './components/main-panel.vue'
import PreviewIframe from './components/PreviewIframe.vue'
import PanelElementOptions from './components/PanelElementOptions.vue'
import PostLock from './components/PostLock.vue'
import { mapGetters, mapActions } from 'vuex'
import keyBindingsMixin from './mixins/keyBindingsMixin.js'
import DeviceElement from './components/DeviceElement.vue'
import { Tooltip, Notice } from '@zb/components'

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
		// RightClickMenu,
		DeviceElement,
		Tooltip,
		Notice
	},
	mixins: [keyBindingsMixin],
	data: () => {
		return Object.assign({
			devicesVisible: false,
			loadedMainApp: false
		}, window.ZnPbInitalData)
	},
	watch: {
		openPanels () {
			this.$nextTick(() => {
				const previewIframeLeft = document.getElementById('znpb-editor-iframe').getBoundingClientRect().left
				this.setRightClickMenu({
					visibility: false,
					previewIframeLeft: previewIframeLeft
				})
			})
		},
		rightClickSource (newValue) {
			if (newValue === 'preview') {
				const previewIframeLeft = document.getElementById('znpb-editor-iframe').getBoundingClientRect().left
				this.setRightClickMenu({
					previewIframeLeft: previewIframeLeft
				})
			}
		}
	},
	computed: {
		...mapGetters([
			'getOpenedPanels',
			'getErrors',
			'isPreviewMode',
			'getRightClickMenu',
			'getElementFocus',
			'getAllContent',
			'getDeviceList',
			'getActiveDevice',
			'getStylesLoading',
			'getPanelPlaceholder',
			'getMainbarPosition'
		]),
		device: function () {
			return this.getActiveDevice.icon
		},
		areaContent () {
			return this.getAllContent['content']
		},
		rootData () {
			return {
				element_type: 'root',
				content: this.areaContent,
				options: {},
				uid: 'contentRoot'
			}
		},
		rightClickSource () {
			if (this.getRightClickMenu) {
				return this.getRightClickMenu.source
			}

			return null
		},
		rightClickVisibility () {
			return !this.isPreviewMode && this.getRightClickMenu && this.getRightClickMenu.visibility && this.getElementFocus
		},
		showEditorTransition: function () {
			if (this.getMainbarPosition === 'left') {
				return 'slide-from-right'
			}
			if (this.getMainbarPosition === 'right') {
				return 'slide-from-left'
			}
			return 'slide-from-right'
		},
		panelPreviewTransition: function () {
			return `znpb-main-panel--out--position-${this.getMainbarPosition}`
		},
		openPanels: {
			get () {
				return this.getOpenedPanels
			},
			set (newOrder) {
				this.savePanelsOrder(newOrder)
			}
		},
		showEditorButtonStyle () {
			const mainBarPosition = this.getMainbarPosition
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
		...mapActions([
			'initialiseDataSets',
			'undo',
			'redo',
			'setIsSavingPage',
			'removeNotice',
			'setPreviewMode',
			'savePanelsOrder',
			'setPageAreas',
			'setPageContent',
			'setActiveArea',
			'addNotice',
			'setElementFocus',
			'setRightClickMenu',
			'setActiveDevice',
			'fetchOptions'
		]),
		activateDevice (device) {
			this.setActiveDevice(device.id)
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
		onResize () {
			if (this.getRightClickMenu && this.getRightClickMenu.visibility) {
				this.setRightClickMenu({
					visibility: false
				})
			}
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

	created: function () {
		this.fetchOptions().finally((result) => {
			this.loadedMainApp = true
		})
		this.initialiseDataSets()
		window.addEventListener('resize', this.onResize)
	},
	beforeUnmount: function () {
		// remove events
		document.removeEventListener('click', this.deselectActiveElement)
		document.removeEventListener('keydown', this.applyShortcuts)
		window.removeEventListener('resize', this.onResize)
	},
	mounted () {
		document.addEventListener('click', this.deselectActiveElement)
		document.addEventListener('keydown', this.applyShortcuts)
		this.addNotice({
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
// @import('./scss/editor.scss');

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
.slide-from-right-enter-active {
	transform: translateX(0);
	transition: all .3s ease-out;
	transition-delay: .1s;
}

.slide-from-right-leave-active {
	transition: all .3s ease-in;
}
.slide-from-right-enter, .slide-from-right-leave-to {
	transform: translateX(60px);
	opacity: 0;
}
/*  slide from left */
.slide-from-left-enter-active {
	transform: translateX(0);
	transition: all .3s ease-out;
	transition-delay: .1s;
}

.slide-from-left-leave-active {
	transition: all .3s ease-in;
}
.slide-from-left-enter, .slide-from-left-leave-to {
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
	z-index: 111;
	width: 5px;
	height: 100%;
	background-color: rgba($secondary, .6);
}
.znpb-panel-placeholder {
	width: 100%;
	height: 100%;
}
</style>
