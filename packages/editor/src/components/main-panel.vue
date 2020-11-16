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
				@mousedown.stop.prevent="togglePanel('panel-tree')"
				v-bind:class="checkActivePanel('panel-tree')"
			>
				<Icon icon="layout"></Icon>
			</div>
			<!-- libary -->
			<div
				@mousedown.stop="openLibraryPanel"
				v-bind:class="checkActivePanel('PanelLibraryModal')"
				class="znpb-editor-header__menu_button"
			>
				<Icon icon="lib"></Icon>
			</div>
			<!-- history -->
			<div
				class="znpb-editor-header__menu_button znpb-editor-header__menu_button--history"
				@mousedown.stop.prevent="togglePanel('panel-history')"
				v-bind:class="checkActivePanel('panel-history')"
			>
				<Icon icon="history"></Icon>
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
				<template v-slot:panel-icon>
					<Icon :icon="device" />
				</template>
				<FlyoutMenuItem
					v-for="(deviceConfig, i) in responsiveDevices"
					v-bind:key="i"
				>
					<DeviceElement :device-config="deviceConfig" />

				</FlyoutMenuItem>
			</FlyoutWrapper>

			<!-- options -->
			<div
				class="znpb-editor-header__menu_button"
				@mousedown.stop="togglePanel('panel-global-settings')"
				v-bind:class="checkActivePanel('panel-global-settings')"
			>
				<Icon icon="sliders" />
			</div>

			<!-- help section -->
			<FlyoutWrapper class="znpb-editor-header__page-save-wrapper">
				<template v-slot:panel-icon>
					<Icon icon="info" />
				</template>
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
				:width="840"
				v-model:show="tourVisibility"
				:title="$translate('getting_started')"
				append-to="#znpb-main-wrapper"
				class="znpb-helpmodal-wrapper"
			>
				<iframe
					width="840"
					height="100%"
					src="https://www.youtube.com/embed/rQ_2lUyhCAY"
					frameborder="0"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
					allowfullscreen
				></iframe>
			</Modal>
			<Modal
				v-model:show="shortcutsModalVisibility"
				:width="560"
				:title="$translate('key_shortcuts')"
				append-to="#znpb-main-wrapper"
			>
				<keyShortcuts />
			</Modal>
			<!-- show about -->
			<Modal
				v-model:show="aboutModalVisibility"
				:width="580"
				:title="$translate('about_zion_builder')"
				:show-maximize="false"
				append-to="#znpb-main-wrapper"
			>
				<aboutModal />
			</Modal>
			<!-- save -->
			<Modal
				v-model:show="helpModalVisibility"
				:width="840"
				:title="$translate('help_modal_title')"
				append-to="#znpb-main-wrapper"
				class="znpb-helpmodal-wrapper"
			>
				<Help></Help>
			</Modal>

			<!-- <ModalTour ref="modalTour" /> -->
			<SaveElementModal />

			<FlyoutWrapper class="znpb-editor-header__page-save-wrapper znpb-editor-header__page-save-wrapper--save">
				<template v-slot:panel-icon>
					<Icon
						v-if="!isSavePageLoading"
						icon="check"
						@mousedown.stop="onSaving"
					/>

					<Loader
						v-else
						:size="12"
					/>
				</template>
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
			<Icon
				icon="more"
				rotate="90"
			/>
		</div>
	</div>
</template>

<script>

import SaveElementModal from './SaveElementModal.vue'
import DeviceElement from './DeviceElement.vue'
import keyShortcuts from './key-shortcuts/keyShortcuts.vue'
import aboutModal from './aboutModal.vue'
import FlyoutWrapper from './FlyoutWrapper.vue'
import FlyoutMenuItem from './FlyoutMenuItem.vue'
import Help from './Help.vue'
// import ModalTour from './ModalTour.vue'
import rafSchd from 'raf-schd'
import { trigger } from '@zb/hooks'
import { useTemplateParts, useSavePage, usePanels, useEditorData, useEditorInteractions, useHistory } from '@composables'
import { translate } from '@zb/i18n'
import { useResponsiveDevices } from '@zb/components'
import { useNotifications } from '@zionbuilder/composables'

export default {
	name: 'ZnpbPanelMain',
	components: {
		DeviceElement,
		FlyoutWrapper,
		FlyoutMenuItem,
		Help,
		keyShortcuts,
		SaveElementModal,
		aboutModal,
		// ModalTour
	},
	data: function () {
		return {
			showModal: false,
			tourVisibility: true,
			aboutModalVisibility: false,
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
	setup () {
		const { saveDraft, savePage, isSavePageLoading } = useSavePage()
		const { togglePanel, openPanels } = usePanels()
		const { activeResponsiveDeviceInfo, responsiveDevices } = useResponsiveDevices()
		const { editorData } = useEditorData()
		const { mainBar, iFrame, getMainbarPosition, getMainBarPointerEvents, getMainBarOrder } = useEditorInteractions()
		const { currentHistoryIndex } = useHistory()
		const saveActions = [
			{
				icon: 'save-template',
				title: translate('save_template'),
				// TODO: implement this
				// action: this.emitEventbus
			},
			{
				icon: 'save-draft',
				title: translate('save_draft'),
				action: saveDraft
			},
			{
				icon: 'save-page',
				title: translate('save_page'),
				action: savePage
			}
		]

		return {
			saveActions,
			isSavePageLoading,
			togglePanel,
			openPanels,
			activeResponsiveDeviceInfo,
			responsiveDevices,
			urls: editorData.value.urls,
			getMainBarOrder,
			getMainBarPointerEvents,
			getMainbarPosition,
			iFrame,
			mainBar,
			currentHistoryIndex,
			savePage
		}
	},
	computed: {

		helpMenuItems () {
			let helpArray = [
				{
					title: this.$translate('help'),
					action: this.showHelpModal
				},
				{
					title: this.$translate('tour'),
					action: this.showTour
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
					url: this.urls.edit_page,
					action: this.helpMenuClick,
					target: '_blank'
				},
				{
					title: this.$translate('back_to_zion_dashboard'),
					url: this.urls.zion_admin,
					action: this.helpMenuClick,
					target: '_blank'
				},
				{
					title: this.$translate('view_post'),
					url: this.urls.preview_url,
					action: this.helpMenuClick,
					target: '_blank'
				}
			]

			return helpArray.filter(item => item.canShow !== false)
		},
		device: function () {
			return this.activeResponsiveDeviceInfo.icon
		},
		orientation: function () {
			return this.activeResponsiveDeviceInfo.isLandscape && this.activeResponsiveDeviceInfo.allowsLandscape
		},
		getCssClasses () {
			let classes = this.isDragging ? 'znpb-editor-panel__container--dragging ' : ''

			if (this.getMainbarPosition() === 'right') {
				classes = classes + 'znpb-editor-header--right '
			}
			if (this.sticked) {
				classes = classes + 'znpb-editor-header--sticked'
			}

			return classes
		},
		helperStyle () {
			const xTranslate = this.getMainbarPosition() === 'right' ? `${this.left + 70 - window.innerWidth}${this.unit}` : `${this.left + 10}${this.unit}`
			return {
				transform: (this.isDragging) ? `translate3d(${xTranslate},${this.top - 22}${this.unit},0)` : null
			}
		},
		panelStyles () {
			return {
				order: this.getMainBarOrder(),
				userSelect: this.userSel,
				pointerEvents: this.isDragging || this.getMainBarPointerEvents() ? 'none' : null
			}
		}
	},
	created () {
		const finishedTour = localStorage.getItem('zion_builder_guided_tour_done')
		if (finishedTour !== null) {
			this.tourVisibility = false
		} else {
			localStorage.setItem('zion_builder_guided_tour_done', false)
			this.tourVisibility = true
		}
	},
	methods: {
		onSaving (status) {
			const { getTemplatePart } = useTemplateParts()
			const contentTemplatePart = getTemplatePart('content')

			if (!contentTemplatePart) {
				console.error('Content template data not found.')
				return
			}

			const pageContent = contentTemplatePart.toJSON()

			this.savePage(pageContent, status)
		},
		showAbout () {
			this.aboutModalVisibility = true
		},
		showTour () {

			// this.$refs.modalTour.show()
			this.tourVisibility = true
			// this.setActiveShowElementsPopup(null)
		},
		showHelpModal () {
			this.helpModalVisibility = true
		},
		checkActivePanel: function (panel) {
			let panelIsOpen = this.openPanels.find(function (panelIsOpen) {
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
			trigger('save-template')
		},

		startDrag (event) {
			this.isDragging = true
			this.iFrame.set('pointerEvents', true)
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
				this.mainBar.set('position', 'right')
				this.mainBar.set('order', 999)
			} else {
				this.mainBar.set('position', 'left')
				this.mainBar.set('order', -999)
			}
		},
		disablePanelMove (event) {
			window.removeEventListener('mousemove', this.rafMovePanel)
			window.removeEventListener('mouseup', this.rafEndDragging)
			this.iFrame.set('pointerEvents', false)
			this.userSel = null
			document.body.style.cursor = null
			this.isDragging = false
			this.$refs.editorHeader.style.transition = null
		},
		openLibraryPanel () {
			this.togglePanel('PanelLibraryModal')
		},
		helpMenuClick () {
			// do nothing
		}
	},
	watch: {
		currentHistoryIndex (newValue) {
			if (this.canAutosave && newValue > 0) {
				const { saveAutosave } = useSavePage()
				saveAutosave()
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
