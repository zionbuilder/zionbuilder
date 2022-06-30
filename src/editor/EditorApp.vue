<template>
	<div
		id="znpb-main-wrapper"
		class="znpb-main-wrapper"
		:class="{
			[`znpb-responsiveDevice--${activeResponsiveDeviceId}`]: activeResponsiveDeviceId,
		}"
	>
		<transition name="slide-from-left">
			<div v-show="UIStore.isPreviewMode" :style="showEditorButtonStyle" class="znpb-editor-layout__preview-buttons">
				<div class="znpb-editor-layout__preview-button" @click="showPanels">
					<Icon icon="layout" />
				</div>
				<!-- devices -->
				<div class="znpb-editor-layout__preview-button" @click="devicesVisible = !devicesVisible">
					<Icon :icon="activeResponsiveDeviceInfo.icon" />
				</div>
				<div v-if="devicesVisible" class="znpb-editor-layout__preview-breakpoints">
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
						<template #content>
							<div
								v-for="(device, index) in responsiveDevices"
								:key="index"
								ref="dropdown"
								class="znpb-options-devices-buttons znpb-has-responsive-options__icon-button"
								:class="{
									'znpb-has-responsive-options__icon-button--active': activeResponsiveDeviceId === device.id,
								}"
								@click="activateDevice(device)"
							>
								<Icon :icon="device.icon" />
							</div>
						</template>
					</Tooltip>
				</div>
			</div>
		</transition>

		<div
			class="znpb-panels-wrapper"
			:class="{
				[`znpb-editorHeaderPosition--${UIStore.mainBar.position}`]: UIStore.mainBar.position,
			}"
		>
			<div
				v-if="UIStore.mainBar.isDragging"
				class="znpb-main-wrapper--mainBarPlaceholder"
				:class="{
					[`znpb-main-wrapper--mainBarPlaceholder--${UIStore.mainBar.draggingPosition}`]:
						UIStore.mainBar.draggingPosition,
				}"
			>
				<div class="znpb-main-wrapper--mainBarPlaceholderInner" />
			</div>

			<MainPanel />

			<!-- center area -->
			<div class="znpb-center-area">
				<div
					v-if="UIStore.panelPlaceholder.visibility"
					id="znpb-panel-placeholder"
					:style="{ left: UIStore.panelPlaceholder.left + 'px' }"
				>
					<div class="znpb-panel-placeholder"></div>
				</div>

				<!-- Start panels -->
				<component
					:is="panelComponentsMap[panel.id]"
					v-for="panel in UIStore.openPanels"
					v-show="!UIStore.isPreviewMode || panel.id === 'preview-iframe'"
					:key="panel.id"
					:panel="panel"
				/>
			</div>
			<!-- end center area -->
		</div>

		<div v-if="UIStore.isPreviewLoading" class="znpb-loading-wrapper-gif">
			<img :src="editorData.urls.loader" />
			<div class="znpb-loading-wrapper-gif__text">{{ translate('generating_preview') }}</div>
		</div>

		<!-- Add Elements Popup -->
		<AddElementPopup />
		<ElementMenu v-if="UIStore.activeElementMenu" />
		<SaveElementModal />
		<PostLock />
		<PanelLibraryModal />

		<!-- notices -->
		<Notice v-for="error in notifications" :key="error.message" :error="error" @close-notice="error.remove()" />

		<div v-if="UIStore.mainBar.isDragging" class="znpb-editor-header__helper" :style="mainBarDraggingPlaceholderStyles">
			<Icon icon="more" rotate="90" />
		</div>
	</div>
	<!-- end znpb-main-wrapper -->
</template>

<script lang="ts" setup>
import { ref, provide, computed, onBeforeUnmount, onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { translate } from '/@/common/modules/i18n';

import PanelTree from './components/treeView/PanelTree.vue';
import PanelGlobalSettings from './components/PanelGlobalSettings.vue';
import PanelHistory from './components/PanelHistory.vue';
import MainPanel from './components/main-panel.vue';
import PreviewIframe from './components/PreviewIframe.vue';
import PanelElementOptions from './components/PanelElementOptions.vue';

// import components
import PanelLibraryModal from './components/PanelLibraryModal.vue';
import PostLock from './components/PostLock.vue';
import SaveElementModal from './components/SaveElementModal.vue';

// Composables
import { AddElementPopup } from './components/AddElementPopup';
import { ElementMenu } from './components/ElementMenu';
import { useKeyBindings, useEditorData, useAutosave, useWindows } from './composables';
import { useResponsiveDevices } from '/@/common/composables';
import { useNotificationsStore } from '/@/common/store';
import { useUIStore, useCSSClassesStore, usePageSettingsStore } from './store';
import { serverRequest } from './api';

// WordPress HeartBeat

const devicesVisible = ref(false);
const UIStore = useUIStore();
const { notifications } = storeToRefs(useNotificationsStore());
const { activeResponsiveDeviceInfo, responsiveDevices, setActiveResponsiveDeviceId, activeResponsiveDeviceId } =
	useResponsiveDevices();
const { applyShortcuts } = useKeyBindings();
const { editorData } = useEditorData();

// Components Map
const panelComponentsMap = {
	'panel-element-options': PanelElementOptions,
	'panel-global-settings': PanelGlobalSettings,
	'preview-iframe': PreviewIframe,
	'panel-history': PanelHistory,
	'panel-tree': PanelTree,
};

// Setup initial data for stores
const cssClasses = useCSSClassesStore();
cssClasses.setCSSClasses(window.ZnPbInitialData.css_classes);

// Setup initial data for page settings
const pageSettings = usePageSettingsStore();
pageSettings.settings = window.ZnPbInitialData.page_settings.values;

const mainBarDraggingPlaceholderStyles = computed(() => {
	return {
		transform: `translate3d(${UIStore.mainBarDraggingPlaceholder.left - 22}px, ${
			UIStore.mainBarDraggingPlaceholder.top - 22
		}px,0)`,
	};
});

// General functionality
useAutosave();

// provide masks for ShapeDividerComponent option
provide('serverRequester', serverRequest);
provide('masks', editorData.value.masks);
provide('plugin_info', editorData.value.plugin_info);

// Add notices
const { add } = useNotificationsStore();
add({
	message: translate('autosave_notice'),
	type: 'info',
	delayClose: 5000,
});

const showEditorButtonStyle = computed(() => {
	let buttonStyle;

	buttonStyle = {
		left: '30px',
		top: '30px',
	};

	return buttonStyle;
});

onMounted(() => {
	document.addEventListener('keydown', applyShortcuts);
});

onBeforeUnmount(() => {
	document.removeEventListener('keydown', applyShortcuts);
});

function activateDevice(device) {
	setActiveResponsiveDeviceId(device.id);
	setTimeout(() => {
		showDevices.value = false;
	}, 50);
}

function showPanels() {
	UIStore.setPreviewMode(false);
}

function showDevices() {
	devicesVisible.value = !devicesVisible.value;
}

// end export default
</script>

<style lang="scss">
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
			width: 36px;
			height: 36px;
			margin-bottom: 3px;
			color: var(--zb-surface-text-color);
			background: var(--zb-surface-color);
			box-shadow: 0 5px 10px 0 var(--zb-surface-shadow);
			border: 1px solid var(--zb-surface-lighter-color);
			border-radius: 50%;
			transition: all 0.15s;

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

.znpb-left-area,
.znpb-top-area,
.znpb-bottom-area,
.znpb-right-area {
	display: flex;
}

.znpb-left-area,
.znpb-right-area {
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

#znpb-html-app {
	overflow: hidden;
}

#znpb-html-app,
#znpb-html-app body {
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
	position: relative;
	display: flex;
	flex: 1 1 auto;
	min-height: 1px;
	min-width: 0;
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
	transition: all 0.3s ease-out;
	transition-delay: 0.1s;
}

.slide-from-right-leave-from {
	transition: all 0.3s ease-in;
}
.slide-from-right-enter-from,
.slide-from-right-leave-to {
	transform: translateX(60px);
	opacity: 0;
}
/*  slide from left */
.slide-from-left-enter-to {
	transform: translateX(0);
	transition: all 0.3s ease-out;
	transition-delay: 0.1s;
}

.slide-from-left-leave-from {
	transition: all 0.3s ease-in;
}
.slide-from-left-enter-from,
.slide-from-left-leave-to {
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
	height: 100%;
	background: #fff;

	img {
		margin-bottom: 30px;
		opacity: 0.6;
	}

	&__text {
		color: #000;
		font-size: 16px;
		font-weight: 300;
	}
}
#znpb-panel-placeholder {
	position: fixed;
	z-index: 99;
	width: 6px;
	height: 100%;
	background-color: var(--zb-secondary-color);
	opacity: 0.5;
}
.znpb-panel-placeholder {
	width: 100%;
	height: 100%;
}
.zbpb-editor-demoMode {
	.znpb-editor-header__page-save-wrapper--save,
	.znpb-editor-header__page-save-wrapper--save a,
	.znpb-editor-header__menu-list li:nth-child(4),
	.znpb-editor-header__menu-list li:nth-child(5),
	.znpb-editor-header__menu-list li:nth-child(6) {
		cursor: not-allowed !important;
	}

	.znpb-editor-header__page-save-wrapper--save .znpb-editor-header__menu_button,
	.znpb-editor-header__page-save-wrapper--save a,
	.znpb-editor-header__menu-list li:nth-child(4) a,
	.znpb-editor-header__menu-list li:nth-child(5) a,
	.znpb-editor-header__menu-list li:nth-child(6) a {
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

.znpb-panels-wrapper {
	display: flex;
	flex-direction: row;
	flex: 1 1 100%;
	height: 100vh;

	&.znpb-editorHeaderPosition--top,
	&.znpb-editorHeaderPosition--bottom {
		flex-direction: column;
	}
}
</style>
