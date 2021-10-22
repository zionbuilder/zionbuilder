<template>
	<div
		class="znpb-editor-header"
		@mousedown.stop.prevent="startBarDrag"
		:class="{
			'znpb-editor-panel__container--dragging': mainBar.isDragging,
			[`znpb-editor-header--${mainBar.position}`]: mainBar.position,
			[`znpb-editor-header--hide-${mainBar.position}`]: isPreviewMode,

		}"
		:style="panelStyles"
		ref="editorHeaderRef"
	>
		<!-- first part -->
		<div class="znpb-editor-header__first">
			<!-- treeview -->
			<div
				class="znpb-editor-header__menu_button znpb-editor-header__menu_button--treeview"
				@mousedown.stop.prevent="togglePanel('panel-tree')"
				:class="{
					'active': openPanelsIDs.includes('panel-tree')
				}"
				v-znpb-tooltip:[tooltipsPosition]="$translate('tree_view')"
			>
				<Icon icon="layout"></Icon>
			</div>
			<!-- libary -->
			<div
				@mousedown.stop="togglePanel('PanelLibraryModal')"
				:class="{
					'active': openPanelsIDs.includes('PanelLibraryModal')
				}"
				class="znpb-editor-header__menu_button"
				v-znpb-tooltip:[tooltipsPosition]="$translate('library')"
			>
				<Icon icon="lib"></Icon>
			</div>
			<!-- history -->
			<div
				class="znpb-editor-header__menu_button znpb-editor-header__menu_button--history"
				@mousedown.stop.prevent="togglePanel('panel-history')"
				:class="{
					'active': openPanelsIDs.includes('panel-history')
				}"
				v-znpb-tooltip:[tooltipsPosition]="$translate('history_panel')"
			>
				<Icon icon="history"></Icon>
			</div>
		</div>
		<!-- center part -->
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
				:class="{
					'active': openPanelsIDs.includes('panel-global-settings')
				}"
				v-znpb-tooltip:[tooltipsPosition]="$translate('page_options')"
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

			<!-- Getting started video -->
			<Modal
				:width="840"
				v-model:show="showGettingStartedVideo"
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
			<!-- key shortcuts modal -->
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

			<FlyoutWrapper
				class="znpb-editor-header__page-save-wrapper znpb-editor-header__page-save-wrapper--save"
				@mousedown="savePage"
			>
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
			v-if="mainBar.isDragging"
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
import { ref, computed, onBeforeUnmount } from 'vue'
import DeviceElement from './DeviceElement.vue'
import keyShortcuts from './key-shortcuts/keyShortcuts.vue'
import aboutModal from './aboutModal.vue'
import FlyoutWrapper from './FlyoutWrapper.vue'
import FlyoutMenuItem from './FlyoutMenuItem.vue'
import Help from './Help.vue'
import { useTemplateParts, useSavePage, usePanels, useEditorData, useEditorInteractions, useSaveTemplate, usePreviewMode } from '@composables'
import { translate } from '@zb/i18n'
import { useResponsiveDevices } from '@zb/components'
import { useBuilderOptions } from '@zionbuilder/composables'

export default {
	name: 'ZnpbPanelMain',
	components: {
		DeviceElement,
		FlyoutWrapper,
		FlyoutMenuItem,
		Help,
		keyShortcuts,
		aboutModal
		//
	},
	setup () {
		const { saveDraft, savePage, isSavePageLoading } = useSavePage()
		const { togglePanel, openPanelsIDs } = usePanels()
		const { activeResponsiveDeviceInfo, responsiveDevices } = useResponsiveDevices()
		const { editorData } = useEditorData()
		const { mainBar, iFrame, getMainbarPosition, getMainBarPointerEvents } = useEditorInteractions()
		const { showSaveElement } = useSaveTemplate()
		const { getOptionValue } = useBuilderOptions()
		const { isPreviewMode } = usePreviewMode()

		// Reactive data
		const editorHeaderRef = ref(null)
		const showGettingStartedVideo = ref(false)

		// Help menu
		const aboutModalVisibility = ref(false)
		const helpModalVisibility = ref(false)
		const shortcutsModalVisibility = ref(false)
		const top = ref(null)
		const left = ref(null)
		const draggingPosition = ref(null)
		const userSel = ref(null)
		const tooltipsPosition = computed(() => {
			if (mainBar.position === 'top') {
				return 'bottom'
			} else if (mainBar.position === 'left') {
				return 'right'
			} else if (mainBar.position === 'right') {
				return 'left'
			} else if (mainBar.position === 'bottom') {
				return 'top'
			}
		})

		// Computed
		const hasWhiteLabel = computed(() => {
			let isPro = editorData.value.plugin_info.is_pro_active
			return isPro && getOptionValue('white_label') !== null && getOptionValue('white_label').plugin_title ? true : false
		})

		const helpMenuItems = computed(() => {
			let helpArray = [
				{
					title: translate('tour'),
					action: doShowGettingStartedVideo,
					canShow: !hasWhiteLabel.value
				},
				{
					title: translate('key_shortcuts'),
					action: () => shortcutsModalVisibility.value = true
				},
				{
					title: translate('about_zion_builder'),
					action: () => aboutModalVisibility.value = true,
					canShow: !hasWhiteLabel.value
				},
				{
					title: translate('back_to_wp_dashboard'),
					url: editorData.value.urls.edit_page,
					action: () => { }
				},
				{
					title: translate('back_to_zion_dashboard'),
					url: editorData.value.urls.zion_admin,
					action: () => { }
				},
				{
					title: translate('view_post'),
					url: editorData.value.urls.preview_url,
					action: () => { },
					target: '_blank'
				}
			]

			return helpArray.filter(item => item.canShow !== false)
		})

		const device = computed(() => {
			return activeResponsiveDeviceInfo.value.icon
		})

		const helperStyle = computed(() => {
			const xTranslate = getMainbarPosition() === 'right' ? `${left.value + 70 - window.innerWidth}px` : `${left.value + 10}px`
			return {
				transform: (mainBar.isDragging) ? `translate3d(${xTranslate},${top.value - 22}px,0)` : null
			}
		})

		const panelStyles = computed(() => {
			return {
				userSelect: userSel.value,
				pointerEvents: mainBar.isDragging || getMainBarPointerEvents() ? 'none' : null
			}
		})

		function saveTemplate () {
			showSaveElement(null)
		}

		const saveActions = [
			{
				icon: 'save-template',
				title: translate('save_template'),
				action: saveTemplate
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


		// Show/Hide getting started video
		if (null === localStorage.getItem('zion_builder_guided_tour_done')) {
			showGettingStartedVideo()
		}

		// Methods
		function doShowGettingStartedVideo () {
			showGettingStartedVideo.value = true
			localStorage.setItem('zion_builder_guided_tour_done', true)
		}

		function startBarDrag () {
			window.addEventListener('mousemove', movePanel)
			window.addEventListener('mouseup', disablePanelMove)

			// disable pointer events on iframe
			iFrame.value.set('pointerEvents', true)
			userSel.value = 'none'
		}

		function movePanel (event) {
			document.body.style.cursor = 'grabbing'

			let newLeft = event.clientX - 30
			let newTop = event.clientY

			// Set a flag so we know that we are dragging
			if (!mainBar.isDragging) {
				mainBar.isDragging = true
			}

			// Calculate horizontal move
			const maxLeft = window.innerWidth - 60
			newLeft = newLeft <= 0 ? 0 : newLeft
			left.value = (newLeft > maxLeft) ? maxLeft : newLeft
			top.value = newTop

			const positions = {
				top: window.innerHeight * 30 / 100 - event.clientY,
				right: event.clientX - window.innerWidth * 70 / 100,
				bottom: event.clientY - window.innerHeight * 70 / 100,
				left: window.innerWidth * 30 / 100 - event.clientX,
			}

			// get all positive values
			const availablePositions = Object.keys(positions).filter(position => {
				return positions[position] > 0
			})

			if (availablePositions.length === 0) {
				return
			}

			const closestPosition = availablePositions.reduce((highest, current) => {
				return positions[highest] > positions[current] ? highest : current
			})

			if (closestPosition) {
				draggingPosition.value = closestPosition
				mainBar.draggingPosition = closestPosition
			}
		}

		function disablePanelMove () {
			window.removeEventListener('mousemove', movePanel)
			window.removeEventListener('mouseup', disablePanelMove)

			// Save the position
			if (draggingPosition.value) {
				mainBar.position = draggingPosition.value
				draggingPosition.value = null
			}

			iFrame.value.set('pointerEvents', false)
			userSel.value = null
			document.body.style.cursor = null
			mainBar.isDragging = false

		}

		function onSaving (status) {
			const { getActivePostTemplatePart } = useTemplateParts()
			const contentTemplatePart = getActivePostTemplatePart()

			if (!contentTemplatePart) {
				console.error('Content template data not found.')
				return
			}

			const pageContent = contentTemplatePart.toJSON()

			savePage(pageContent, status)
		}


		function onAfterLeave () {
			const el = document.querySelector('iframe')
			el.style.transform = 'translateZ(0)'
		}

		function onAfterEnter () {
			const el = document.querySelector('iframe')
			el.style.transform = null
		}

		// Lifecycle
		onBeforeUnmount(() => {
			window.removeEventListener('mousemove', movePanel)
			window.removeEventListener('mouseup', disablePanelMove)
		})

		return {
			// Refs
			showGettingStartedVideo,
			editorHeaderRef,
			aboutModalVisibility,
			helpModalVisibility,
			shortcutsModalVisibility,
			mainBar,
			isPreviewMode,
			draggingPosition,

			// Computed
			openPanelsIDs,
			helpMenuItems,
			device,
			panelStyles,
			helperStyle,
			tooltipsPosition,

			// Methods
			saveActions,
			isSavePageLoading,
			togglePanel,
			responsiveDevices,
			savePage,
			onSaving,
			startBarDrag,
			onAfterLeave,
			onAfterEnter

		}
	}
}

</script>
<style lang="scss">
.znpb-editor-header {
	position: relative;
	z-index: 10;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	width: 60px;
	color: var(--zb-primary-text-color);
	background: var(--zb-primary-color);
	transition: margin .3s ease-out;
	cursor: move;
	user-select: none;

	// Bar positions
	&--right {
		order: 9999;
	}

	&--top {
		top: 0;
		left: 0;
		flex-direction: row;
		order: 0;
		width: 100%;
		height: 60px;

		& .znpb-editor-header__first, & .znpb-editor-header__last {
			flex-direction: row;
		}
	}

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
		color: var(--zb-primary-text-color);
		font-size: 16px;
		transition: background-color .15s ease;
		cursor: pointer;

		&:hover, &:focus, &:active {
			background-color: var(--zb-primary-hover-color);
		}

		&.active {
			background-color: var(--zb-primary-hover-color);
		}
		.znpb-editor-icon-wrapper {
			font-size: 16px;
		}

		&.center-button {
			width: 44px;
			height: 44px;
			margin: 0 auto;
			color: var(--zb-primary-text-color);
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
				background: var(--zb-primary-hover-color);
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
		opacity: .5;
	}
}
.znpb-editor-header__helper {
	position: absolute;
	z-index: 11;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 44px;
	height: 44px;
	background: var(--zb-primary-color);
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

// Hide animation
.znpb-editor-header--hide {
	&-left {
		margin-left: -60px;
	}

	&-right {
		margin-right: -60px;
	}
}

// Dragging helpers
.znpb-main-wrapper--mainBarPlaceholder {
	position: absolute;
	z-index: 999999;
	background: var(--zb-secondary-color);
}
.znpb-main-wrapper--mainBarPlaceholder--top {
	top: 0;
	left: 0;
	width: 100%;
	height: 5px;
	animation: .3s ease-out 0s 1 slideInFromTop;
}

.znpb-main-wrapper--mainBarPlaceholder--left {
	top: 0;
	left: 0;
	width: 5px;
	height: 100%;
	animation: .3s ease-out 0s 1 slideInFromLeft;
}

.znpb-main-wrapper--mainBarPlaceholder--right {
	top: 0;
	right: 0;
	width: 5px;
	height: 100%;
	animation: .3s ease-out 0s 1 slideInFromRight;
}

.znpb-main-wrapper--mainBarPlaceholder--bottom {
	bottom: 0;
	left: 0;
	width: 100%;
	height: 5px;
	animation: .3s ease-out 0s 1 slideInFromBottom;
}

@keyframes slideInFromLeft {
	0% {
		transform: translateX(-100%);
	}
	100% {
		transform: translateX(0);
	}
}
@keyframes slideInFromRight {
	0% {
		transform: translateX(100%);
	}
	100% {
		transform: translateX(0);
	}
}
@keyframes slideInFromTop {
	0% {
		transform: translateY(-100%);
	}
	100% {
		transform: translateY(0);
	}
}
@keyframes slideInFromBottom {
	0% {
		transform: translateY(100%);
	}
	100% {
		transform: translateY(0);
	}
}
</style>
