<template>
	<div
		id="preview-iframe"
		ref="root"
		class="znpb-editor-iframe-wrapper"
		:style="pointerEvents"
		:class="getWrapperClasses"
	>
		<iframe
			v-if="urls.preview_frame_url"
			id="znpb-editor-iframe"
			ref="iframe"
			:src="urls.preview_frame_url"
			:style="iframeStyles"
			@load="checkIframeLoading"
		/>

		<teleport v-if="iframeAPP" :to="iframeAPP">
			<PreviewApp />
		</teleport>
	</div>
</template>

<script>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';
import { addAction, removeAction } from '/@/common/modules/hooks';
import { each } from 'lodash-es';

import { useKeyBindings, useSavePage, useEditorData, useWindows } from '../composables';
import { useResponsiveDevices } from '/@/common/composables';
import { useUIStore, useContentStore, useElementDefinitionsStore, useHistoryStore } from '../store';

import PreviewApp from '/@/preview/PreviewApp.vue';

export default {
	name: 'PreviewIframe',
	components: {
		PreviewApp,
	},
	setup() {
		const {
			activeResponsiveDeviceInfo,
			iframeWidth,
			setCustomIframeWidth,
			scaleValue,
			autoScaleActive,
			activeResponsiveDeviceId,
			ignoreWidthChangeFlag,
			setCustomScale,
		} = useResponsiveDevices();
		const { applyShortcuts } = useKeyBindings();
		const { saveAutosave } = useSavePage();
		const { editorData } = useEditorData();

		const UIStore = useUIStore();
		const contentStore = useContentStore();
		const elementsDefinitionsStore = useElementDefinitionsStore();
		const { addWindow, addEventListener, removeEventListener, getWindows, removeWindow } = useWindows();

		// Dom refs
		const iframeAPP = ref(null);
		const root = ref(null);
		const iframe = ref(null);
		const containerSize = ref({
			width: 0,
			height: 0,
		});
		const iframeSize = ref({
			width: 0,
			height: 0,
		});

		const getWrapperClasses = computed(() => {
			const { width: containerWidth } = containerSize.value;
			const { width: iframeWidth } = iframeSize.value;

			return {
				[`znpb-editor-iframe-wrapper--${activeResponsiveDeviceInfo.value.id}`]: true,
				'znpb-editor-iframe--isAutoscale': autoScaleActive.value,
				'znpb-editor-iframe--alignStart': Math.round((scaleValue.value / 100) * iframeWidth) > containerWidth,
				'znpb-editor-iframe--hideOverflow': Math.round((scaleValue.value / 100) * iframeWidth) <= containerWidth,
			};
		});

		const deviceStyle = computed(() => {
			let styles = {};

			return styles;
		});

		// Change the device width if it is the currently selected device
		addAction('zionbuilder/responsive/change_device_width', (device, newValue, oldValue) => {
			if (device.id === activeResponsiveDeviceId.value && newValue !== oldValue) {
				setCustomIframeWidth(newValue);
			}
		});

		const iframeStyles = computed(() => {
			const styles = {};

			if (root.value) {
				const { height: containerHeight } = containerSize.value;

				let height = 0;
				if (activeResponsiveDeviceInfo.value && activeResponsiveDeviceInfo.value.height) {
					height = activeResponsiveDeviceInfo.value.height;
				} else {
					height = containerHeight;
				}

				if (iframeWidth.value) {
					styles.width = `${iframeWidth.value}px`;
				}

				const scale = scaleValue.value / 100;
				styles.transform = `scale(${scale})`;

				// Set the proper height
				styles.height = `${(100 / scaleValue.value) * height}px`;
				styles.maxHeight = `${(100 / scaleValue.value) * containerHeight}px`;
			}

			return styles;
		});

		// Prevent empty bars when enabling preview mode
		// @see https://hogash.atlassian.net/browse/ZB-2981
		watch(
			() => UIStore.isPreviewMode,
			newValue => {
				if (newValue && activeResponsiveDeviceInfo.value.id === 'default') {
					console.log('new Value');
					nextTick(() => {
						iframeWidth.value = null;
					});
				}
			},
		);

		// Watch if the scale is manually changed
		watch([containerSize, iframeSize], ([containerNewSize, iframeNewSize]) => {
			if (autoScaleActive.value) {
				let scale = (containerNewSize.width / iframeNewSize.width) * 100;
				scale = scale > 100 ? 100 : scale;
				setCustomScale(scale);
			}
		});

		// Watch for container width change and set the new width
		const containerResizeObserver = new ResizeObserver(entries => {
			for (let entry of entries) {
				if (entry.contentBoxSize) {
					// Firefox implements `contentBoxSize` as a single content rect, rather than an array
					const contentBoxSize = Array.isArray(entry.contentBoxSize) ? entry.contentBoxSize[0] : entry.contentBoxSize;

					containerSize.value = {
						width: contentBoxSize.inlineSize,
						height: contentBoxSize.blockSize,
					};
				} else {
					containerSize.value = {
						width: entry.contentRect.width,
						height: entry.contentRect.width,
					};
				}
			}
		});

		const iframeResizeObserver = new ResizeObserver(entries => {
			for (let entry of entries) {
				if (entry.contentBoxSize) {
					// Firefox implements `contentBoxSize` as a single content rect, rather than an array
					const contentBoxSize = Array.isArray(entry.contentBoxSize) ? entry.contentBoxSize[0] : entry.contentBoxSize;

					iframeSize.value = {
						width: contentBoxSize.inlineSize,
						height: contentBoxSize.blockSize,
					};
				} else {
					iframeSize.value = {
						width: entry.contentRect.width,
						height: entry.contentRect.width,
					};
				}
			}
		});

		/**
		 * Change the device width
		 */
		watch(activeResponsiveDeviceId, () => {
			// Don't change the width if we receive an ignore
			if (ignoreWidthChangeFlag.value) {
				ignoreWidthChangeFlag.value = false;
				return;
			}

			if (activeResponsiveDeviceInfo.value.width) {
				setCustomIframeWidth(activeResponsiveDeviceInfo.value.width);
			} else if (activeResponsiveDeviceInfo.value.id === 'default') {
				setCustomIframeWidth(containerSize.value.width < 1200 ? 1200 : containerSize.value.width);
			}
		});

		// Prevent bug when scroll is moved and enable autoscale
		watch(autoScaleActive, newValue => {
			if (newValue) {
				root.value.scrollLeft = 0;
			} else {
				setCustomScale(100);
			}
		});

		// Lifecycle
		onMounted(() => {
			const { width, height } = root.value.getBoundingClientRect();
			containerSize.value = {
				width,
				height,
			};

			const { width: iframeWidth } = iframe.value.getBoundingClientRect();
			// Set the desktop width
			iframeSize.value = {
				width,
				height,
			};

			setCustomIframeWidth(iframeWidth < 1200 ? 1200 : iframeWidth);

			// Attach observer
			containerResizeObserver.observe(root.value);
			iframeResizeObserver.observe(iframe.value);
		});

		onBeforeUnmount(() => {
			containerResizeObserver.unobserve(root.value);
			containerResizeObserver.observe(iframe.value);
		});

		const pointerEvents = computed(() => {
			let style = {};

			if (UIStore.iFrame.pointerEvents) {
				style.pointerEvents = 'none';
			}
			style.order = UIStore.getPanelOrder('preview-iframe');
			return style;
		});

		return {
			iframeAPP,
			activeResponsiveDeviceInfo,
			applyShortcuts,
			saveAutosave,
			pageId: editorData.value.page_id,
			urls: editorData.value.urls,
			addWindow,
			getWindows,
			addEventListener,
			removeEventListener,
			removeWindow,

			// Dom refs
			root,
			iframe,

			// computed
			deviceStyle,
			getWrapperClasses,
			iframeStyles,
			pointerEvents,

			// Iframe size tooltip
			iframeWidth,

			// Stores
			contentStore,
			UIStore,
			elementsDefinitionsStore,
		};
	},
	data() {
		return {
			ignoreNextReload: false,
			localStoragePageData: {},
			iframeLoaded: false,
		};
	},
	// end checkMousePosition
	beforeUnmount() {
		if (this.getWindows('preview')) {
			this.getWindows('preview').removeEventListener('keydown', this.applyShortcuts);
			this.getWindows('preview').removeEventListener('click', this.preventClicks, true);
			this.getWindows('preview').removeEventListener('beforeunload', this.onBeforeUnloadIframe);
			this.getWindows('preview').removeEventListener('click', this.onIframeClick, true);
		}

		removeAction('refreshIframe', this.refreshIframe);
	},
	mounted() {
		addAction('refreshIframe', this.refreshIframe);
	},
	methods: {
		setPageContent(areas) {
			// New system
			each(areas, (areaContent, id) => {
				this.contentStore.registerArea(
					{
						name: id,
						id: id,
					},
					areaContent,
				);
			});

			// Add timestamp for content
			this.UIStore.setContentTimestamp();
		},

		onIframeClick(event) {
			this.root.click();
		},
		onIframeLoaded() {
			this.iframeLoaded = true;
			const iframeWindow = this.$refs.iframe.contentWindow;

			// Expose common methods
			const elementDefinitionsStore = useElementDefinitionsStore();
			elementDefinitionsStore.setCategories(iframeWindow.ZnPbInitialData.elements_categories);
			elementDefinitionsStore.addElements(iframeWindow.ZnPbInitialData.elements_data);

			// Register the document
			this.addWindow('preview', iframeWindow);
			this.attachIframeEvents();

			// Set preview data
			// Render the app
			const renderElement = iframeWindow.document.getElementById(`znpb-preview-${window.ZnPbInitialData.page_id}-area`);
			if (renderElement) {
				this.iframeAPP = iframeWindow.document.getElementById(`znpb-preview-${window.ZnPbInitialData.page_id}-area`);
			} else {
				console.log('preview element not found');
			}

			// Set the page content if this was not reloaded by an option
			if (!this.ignoreNextReload) {
				this.setPageContent(iframeWindow.ZnPbInitialData.page_content);
			}

			this.ignoreNextReload = false;
			this.UIStore.setPreviewLoading(false);

			// Save the timestamp so we can use if in various ways. One example is the tree view justAdded element feature
			this.UIStore.setLoadTimestamp();
		},
		attachIframeEvents() {
			this.getWindows('preview').addEventListener('click', this.preventClicks, true);
			this.getWindows('preview').addEventListener('keydown', this.applyShortcuts);
			this.getWindows('preview').addEventListener('beforeunload', this.onBeforeUnloadIframe, { capture: true });
			this.getWindows('preview').addEventListener('click', this.onIframeClick, true);
		},
		preventClicks(event) {
			const e = window.e || event;
			if (e.target.tagName === 'a' || !e.target.classList.contains('znpb-allow-click')) {
				e.preventDefault();
			}
		},
		onBeforeUnloadIframe(event) {
			const historyStore = useHistoryStore();
			if (historyStore.isDirty) {
				event.preventDefault();
				event.returnValue = 'Do you want to leave this site? Changes you made may not be saved.';
			} else {
				this.UIStore.setPreviewLoading(true);
			}
		},
		refreshIframe() {
			this.saveAutosave().then(() => {
				window.location.reload();
			});
		},
		checkIframeLoading() {
			if (this.$refs.iframe && this.$refs.iframe.contentDocument) {
				if (this.$refs.iframe.contentDocument.readyState === 'complete') {
					this.onIframeLoaded();
				} else {
					setTimeout(this.checkIframeLoading, 100);
				}
			} else {
				setTimeout(this.checkIframeLoading, 100);
			}
		},
	},
};
</script>

<style lang="scss">
.znpb-editor-iframe-wrapper {
	position: relative;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	order: 3;
	flex: 1 1 0;
	overflow-x: auto;
	overflow-y: hidden;
	min-width: 240px;
	background-color: var(--zb-surface-darker-color);
}

.znpb-editor-iframe-wrapperContainer {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	width: 100%;
	height: 100%;
	margin: 0 auto;
}

.znpb-editor-iframe--isAutoscale {
	overflow: hidden;
}

.znpb-editor-iframe--isAutoscale #znpb-editor-iframe {
	transform-origin: center;

	#znpb-editor-iframe {
		margin: initial;
	}
}

.znpb-editor-iframe--alignStart {
	& #znpb-editor-iframe {
		transform-origin: center left;
	}
}

// Iframe
#znpb-editor-iframe {
	flex-grow: 0;
	flex-shrink: 0;
	width: 100%;
	height: 100%;
	margin: auto;
	box-shadow: 0 0 60px 0 rgba(0, 0, 0, 0.1);
	border: 0;
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

.znpb-editor-iframe--hideOverflow {
	overflow-x: clip;
	overflow-y: clip;

	#znpb-editor-iframe {
		margin: initial;
	}
}

#preview-iframe {
	overflow-y: clip;
}
</style>
