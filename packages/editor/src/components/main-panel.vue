<template>
	<div
		class="znpb-editor-header"
		@mousedown.stop.prevent="startDrag"
		:class="getCssClasses"
		:style="panelStyles"
		ref="editorHeader"
	>
		<!-- first part -->
		<div class="znpb-editor-header__first">
			<!-- treeview -->
			<div
				class="znpb-editor-header__menu_button znpb-editor-header__menu_button--treeview"
				@mousedown.stop.prevent="togglePanel('panel-tree', false)"
				v-bind:class="checkActivePanel('panel-tree')"
			>
				<BaseIcon icon="layout"></BaseIcon>
			</div>
			<!-- libary -->
			<div
				@mousedown.stop="openLibraryPanel"
				v-bind:class="checkActivePanel('PanelLibraryModal')"
				class="znpb-editor-header__menu_button"
			>
				<BaseIcon icon="lib"></BaseIcon>
			</div>
			<!-- history -->
			<div
				class="znpb-editor-header__menu_button znpb-editor-header__menu_button--history"
				@mousedown.stop.prevent="togglePanel('panel-history', false)"
				v-bind:class="checkActivePanel('panel-history')"
			>
				<BaseIcon icon="history"></BaseIcon>
			</div>
		</div>
		<!-- center part -->
		<div class="znpb-editor-header__center">
			<!-- add elements -->

		</div>
		<!-- last part -->
		<div class="znpb-editor-header__last">
			<!-- devices -->
			<FlyoutWrapper>
				<BaseIcon
					slot="panel-icon"
					:icon="device"
				></BaseIcon>
				<FlyoutMenuItem
					v-for="(deviceConfig, i) in getDeviceList"
					v-bind:key="i"
				>
					<DeviceElement :device-config="deviceConfig" />

				</FlyoutMenuItem>
			</FlyoutWrapper>

			<!-- options -->
			<div
				class="znpb-editor-header__menu_button"
				@mousedown.stop="togglePanel('panel-global-settings', false)"
				v-bind:class="checkActivePanel('panel-global-settings')"
			>
				<BaseIcon icon="sliders" />
			</div>

			<!-- help section -->
			<FlyoutWrapper class="znpb-editor-header__page-save-wrapper">
				<BaseIcon
					icon="info"
					slot="panel-icon"
				/>
				<FlyoutMenuItem
					v-for="(menuItem, i) in helpMenuItems"
					:key="i"
				>
					<a
						@mousedown.prevent.stop="menuItem.action"
						:href="menuItem.url"
						:target="menuItem.target"
					>
						<span>{{menuItem.title}}</span>
					</a>
				</FlyoutMenuItem>
			</FlyoutWrapper>
			<Modal
				:show.sync="shortcutsModalVisibility"
				:width="560"
				:title="$translate('key_shortcuts')"
				append-to="#znpb-main-wrapper"
			>
				<keyShortcuts />
			</Modal>
			<!-- show about -->
			<Modal
				:show.sync="aboutModalVisibility"
				:width="580"
				:title="$translate('about_zion_builder')"
				:show-maximize="false"
				append-to="#znpb-main-wrapper"
			>
				<aboutModal />
			</Modal>
			<!-- save -->
			<Modal
				:show.sync="helpModalVisibility"
				:width="840"
				:title="$translate('help_modal_title')"
				append-to="#znpb-main-wrapper"
				class="znpb-helpmodal-wrapper"
			>
				<Help></Help>
			</Modal>

			<ModalTour ref="modalTour" />
			<SaveElementModal :template="true" />

			<FlyoutWrapper class="znpb-editor-header__page-save-wrapper znpb-editor-header__page-save-wrapper--save">

				<BaseIcon
					v-if="!getSavingPage"
					icon="check"
					slot="panel-icon"
					@mousedown.stop="onSaving"
				/>
				<Loader
					v-else
					slot="panel-icon"
					:size="12"
				/>
				<FlyoutMenuItem
					v-for="(menuItem, i) in saveActions"
					v-bind:key="i"
				>
					<a
						@mousedown.stop="menuItem.action"
						href="#"
					>
						<span>{{menuItem.title}}</span>
					</a>
				</FlyoutMenuItem>
			</FlyoutWrapper>
		</div>
		<!-- end last part -->
		<!-- helper -->
		<div
			v-if="isDragging"
			class="znpb-editor-header__helper"
			:style="helperStyle"
		>
			<BaseIcon
				icon="more"
				rotate="90"
			/>
		</div>
	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import SaveElementModal from './SaveElementModal.vue'
import DeviceElement from './DeviceElement.vue'
import keyShortcuts from './key-shortcuts/keyShortcuts.vue'
import aboutModal from './aboutModal.vue'
import FlyoutWrapper from './FlyoutWrapper.vue'
import FlyoutMenuItem from './FlyoutMenuItem.vue'
import Help from './Help.vue'
import { Modal, Loader } from '@zb/components'
import ModalTour from './ModalTour.vue'
import rafSchd from 'raf-schd'

export default {
	name: 'ZnpbPanelMain',
	components: {
		DeviceElement,
		FlyoutWrapper,
		FlyoutMenuItem,
		Help,
		keyShortcuts,
		Modal,
		SaveElementModal,
		aboutModal,
		ModalTour,
		Loader
	},
	props: {

	},
	data: function () {
		return {
			showModal: false,
			aboutModalVisibility: false,
			saveActions: [
				{
					icon: 'save-template',
					title: this.$translate('save_template'),
					action: this.emitEventbus
				},
				{
					icon: 'save-draft',
					title: this.$translate('save_draft'),
					action: this.onSavePage
				},
				{
					icon: 'save-page',
					title: this.$translate('save_page'),
					action: this.onSave
				}
			],
			helpModalVisibility: false,
			shortcutsModalVisibility: false,
			canAutosave: true,
			unit: 'px',
			top: null,
			left: null,
			isDragging: false,
			userSel: null,
			height: null,
			sticked: false
		}
	},

	computed: {
		...mapGetters([
			'getMainBarOrder',
			'getMainBarPointerEvents',
			'getDeviceList',
			'getActiveDevice',
			'getOpenedPanels',
			'getIsSavingPage',
			'activeHistoryIndex',
			'getPageContent',
			'getEditPageUrl',
			'getZionAdminUrl',
			'getPreviewUrl',
			'getMainbarPosition'
		]),
		pageCont () {
			return this.getPageContent['contentRoot'] !== undefined ? this.getPageContent['contentRoot'].content : []
		},

		helpMenuItems () {
			let helpArray = [
				// {
				// 	title: this.$translate('help'),
				// 	action: this.showHelpModal
				// },
				{
					title: this.$translate('tour'),
					action: this.showTour,
					canShow: this.pageCont !== undefined && this.pageCont.length === 0
				},
				{
					title: this.$translate('key_shortcuts'),
					action: this.showShortcutsModal
				},
				{
					title: this.$translate('about_zion_builder'),
					action: this.showAbout
				},
				{
					title: this.$translate('back_to_wp_dashboard'),
					url: this.getEditPageUrl,
					action: this.helpMenuClick,
					target: '_blank'
				},
				{
					title: this.$translate('back_to_zion_dashboard'),
					url: this.getZionAdminUrl,
					action: this.helpMenuClick,
					target: '_blank'
				},
				{
					title: this.$translate('view_post'),
					url: this.getPreviewUrl,
					action: this.helpMenuClick,
					target: '_blank'
				}
			]

			return helpArray.filter(item => item.canShow !== false)
		},
		device: function () {
			return this.getActiveDevice.icon
		},
		orientation: function () {
			return this.getActiveDevice.isLandscape && this.getActiveDevice.allowsLandscape
		},
		getSavingPage () {
			return this.getIsSavingPage
		},
		getCssClasses () {
			let classes = this.isDragging ? 'znpb-editor-panel__container--dragging ' : ''

			if (this.getMainbarPosition === 'right') {
				classes = classes + 'znpb-editor-header--right '
			}
			if (this.sticked) {
				classes = classes + 'znpb-editor-header--sticked'
			}

			return classes
		},
		helperStyle () {
			const xTranslate = this.getMainbarPosition === 'right' ? `${this.left + 70 - window.innerWidth}${this.unit}` : `${this.left + 10}${this.unit}`
			return {
				transform: (this.isDragging) ? `translate3d(${xTranslate},${this.top - 22}${this.unit},0)` : null
			}
		},
		panelStyles () {
			return {
				order: this.getMainBarOrder,
				userSelect: this.userSel,
				pointerEvents: this.isDragging || this.getMainBarPointerEvents ? 'none' : null
			}
		}
	},
	methods: {
		...mapActions([
			'togglePanel',
			'savePage',
			'setIframePointerEvents',
			'setMainbarOrder',
			'setElementConfigForLibrary',
			'setActiveShowElementsPopup',
			'addNotice',
			'setMainbarPosition'
		]),
		onSaving (status) {
			this.savePage(this, {
				status
			}).catch(error => {
				this.addNotice({
					message: error.message,
					type: 'error',
					delayClose: 5000
				})
			}).finally(() => {
				const noticeText = status
				this.addNotice({
					message: status === 'publish' ? this.$translate('page_saved_publish') : this.$translate('page_saved'),
					delayClose: 5000
				}).then(() => {
					this.isDisplayingSaveNotice = false
				})
			})
		},
		onSave () {
			this.onSaving('publish')
		},
		onSavePage () {
			this.onSaving('autosave')
		},
		showAbout () {
			this.aboutModalVisibility = true
		},
		showTour () {
			localStorage.setItem('zion_builder_guided_tour_done', false)
			this.$refs.modalTour.show()

			this.setActiveShowElementsPopup(null)
		},
		showHelpModal () {
			this.helpModalVisibility = true
		},
		checkActivePanel: function (panel) {
			let panelIsOpen = this.getOpenedPanels.find(function (panelIsOpen) {
				return panelIsOpen.id === panel
			})
			return {
				'active': panelIsOpen
			}
		},
		showShortcutsModal () {
			this.shortcutsModalVisibility = true
		},
		emitEventbus (event) {
			window.ZionBuilderApi.trigger('save-template')
		},

		startDrag (event) {
			this.isDragging = true
			this.setIframePointerEvents(true)
			this.rafMovePanel = rafSchd(this.movePanel)
			this.rafEndDragging = rafSchd(this.disablePanelMove)
			window.addEventListener('mousemove', this.rafMovePanel)
			window.addEventListener('mouseup', this.rafEndDragging)
			// disable pointer events on iframe
			this.sticked = false
			this.userSel = 'none'
		},
		movePanel (event) {
			document.body.style.cursor = 'grabbing'
			this.$refs.editorHeader.style.transition = 'none'
			let newLeft = event.clientX - 30
			let newTop = event.clientY

			// Calculate horizontal move
			const maxLeft = window.innerWidth - 60
			newLeft = newLeft <= 0 ? 0 : newLeft
			this.left = (newLeft > maxLeft) ? maxLeft : newLeft
			this.top = newTop

			if (event.clientX > window.innerWidth / 2) {
				this.setMainbarPosition('right')
				this.setMainbarOrder(999)
			} else {
				this.setMainbarPosition('left')
				this.setMainbarOrder(-999)
			}
		},
		disablePanelMove (event) {
			window.removeEventListener('mousemove', this.rafMovePanel)
			window.removeEventListener('mouseup', this.rafEndDragging)
			this.setIframePointerEvents(false)
			this.userSel = null
			document.body.style.cursor = null
			this.isDragging = false
			this.$refs.editorHeader.style.transition = null
		},
		openLibraryPanel () {
			// Set the active element library
			this.setElementConfigForLibrary({
				parentUid: 'contentRoot',
				index: -1
			})
			this.togglePanel('PanelLibraryModal', false)
		},
		helpMenuClick () {
			// do nothing
		}
	},
	watch: {
		activeHistoryIndex (newValue) {
			if (this.canAutosave && newValue > 0) {
				this.savePage({
					status: 'autosave',
					showPreloader: false
				})
				this.canAutosave = false

				setTimeout(() => {
					this.canAutosave = true
				}, window.ZnPbInitalData.autosaveInterval * 1000)
			}
		}
	},
	beforeUnmount () {
		this.disablePanelMove()
	}
}

</script>
<style lang="scss">
.znpb-editor-header {
	position: relative;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	width: 60px;
	color: $primary-color--accent;
	background: $primary-color;
	transition: margin .3s ease-out;
	cursor: move;
	user-select: none;

	&__first {
		display: flex;
		flex-direction: column;
		align-items: center;
		flex: 2;
	}

	&__center {
		display: flex;
		align-items: center;
		flex: 1;
	}

	&__last {
		position: relative;
		display: flex;
		flex-direction: column;
		justify-content: flex-end;
		align-items: center;
		flex: 2;
		.znpb-admin-small-loader {
			top: calc(50% - 2px);
			left: calc(50% - 2px);
			width: 18px;
			height: 18px;
		}
	}

	&__menu_button {
		position: relative;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 60px;
		height: 60px;
		color: $primary-color--accent;
		font-size: 16px;
		transition: background-color .15s ease;
		cursor: pointer;

		&:hover, &:focus, &:active {
			background-color: $primary-variant;
		}

		&.active {
			background-color: $primary-variant;
		}
		.znpb-editor-icon-wrapper {
			font-size: 16px;
		}

		&.center-button {
			width: 44px;
			height: 44px;
			margin: 0 auto;
			color: #fff;
			background-color: transparent;

			.znpb-editor-icon-wrapper {
				position: relative;
				transition: transform .2s ease;
			}

			&:before {
				content: "";
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: $primary-variant;
				border-radius: 50%;
				transition: transform .2s ease;
			}

			&:hover:before {
				transform: scale(1.1);
			}
		}
		&.center-button.active {
			background-color: transparent;

			.znpb-editor-icon-wrapper {
				transform: rotate(45deg);
			}
		}

		.znpb-loader {
			top: -3px;
			&::before, &::after {
				border-color: transparent;
			}
			&::after {
				border-right-color: #fff;
				border-bottom-color: #fff;
			}
		}
	}
	&.znpb-editor-panel__container--dragging {
		z-index: 1000;
		width: auto;
		height: auto;
		opacity: .5;
		&.znpb-editor-header--sticked {
			background: $primary-color;
		}
	}
}
.znpb-editor-header__helper {
	position: absolute;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 44px;
	height: 44px;
	background: $primary-color;
	border-radius: 50%;
	opacity: 1;
}

.znpb-top-area, .znpb-bottom-area {
	width: 100%;

	.znpb-editor-header {
		flex-direction: row;
		align-items: center;
		width: 100%;
		height: 60px;

		&__first {
			flex-direction: row;
		}
		&__center {
			flex-direction: row;
		}
		&__last {
			flex-direction: row;
		}
	}
}
</style>
