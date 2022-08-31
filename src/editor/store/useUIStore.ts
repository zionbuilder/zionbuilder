import { defineStore } from 'pinia';
import { filter, get } from 'lodash-es';
import { useUserData } from '../composables/useUserData';
import { useResponsiveDevices } from '/@/common/composables';
interface Panel {
	id: string;
	component: string;
	position: string;
	isDetached: boolean;
	isDragging: boolean;
	isExpanded: boolean;
	isActive: boolean;
	width: number;
	height: number;
	group: string;
	saveOpenState: boolean;
	offsets: {
		posX: number;
		posY: number;
	};
	[key: string]: unknown;
}

export const useUIStore = defineStore('ui', {
	state: (): {
		panelsOrder: string[];
		panelPlaceholder: Record<string, number>;
		panels: Panel[];
		mainBar: {
			position: string;
			pointerEvents: boolean;
			draggingPosition: string | null;
		};
		mainBarDraggingPlaceholder: {
			top: number | null;
			left: number | null;
		};
		iFrame: {
			pointerEvents: null | boolean;
		};
		isLibraryOpen: boolean;
		isPreviewMode: boolean;
		isPreviewLoading: boolean;
		loadTimestamp: number;
		contentTimestamp: number;
		editedElement: ZionElement | null;
		activeElementMenu: {
			element: ZionElement;
			selector: HTMLElement;
			actions: Record<string, unknown>;
			rand: number;
		} | null;
		isElementDragging: boolean;
		activeAddElementPopup: Record<string, unknown> | null;
		libraryInsertConfig: {
			element?: ZionElement;
			index?: number;
		};
	} => {
		const { getUserData } = useUserData();
		const UIUserData = getUserData();

		function getPanelData(panelID: string, extraData: Record<string, unknown>): Panel {
			return Object.assign(
				{},
				{
					id: panelID,
					position: 'relative',
					isDetached: false,
					isDragging: false,
					isExpanded: false,
					isActive: false,
					width: 360,
					height: null,
					group: null,
					saveOpenState: true,
					offsets: {
						posX: null,
						posY: null,
					},
				},
				extraData,
				get(UIUserData, `panels.${panelID}`, {}),
			);
		}

		const panels: Panel[] = [
			getPanelData('panel-element-options', {
				saveOpenState: false,
			}),
			getPanelData('panel-global-settings', {
				saveOpenState: false,
			}),
			getPanelData('preview-iframe', {
				isActive: true,
			}),
			getPanelData('panel-history', {}),
			getPanelData('panel-tree', {}),
		];

		return {
			panelsOrder: get(UIUserData, 'panelsOrder', [
				'panel-element-options',
				'panel-global-settings',
				'preview-iframe',
				'panel-history',
				'panel-tree',
			]),
			panelPlaceholder: {},
			panels,
			mainBar: {
				position: 'left',
				pointerEvents: false,
				draggingPosition: null,
				...(UIUserData.mainBar || {}),
			},
			mainBarDraggingPlaceholder: {
				top: null,
				left: null,
			},
			iFrame: {
				pointerEvents: false,
			},
			isLibraryOpen: false,
			isPreviewMode: false,
			loadTimestamp: 0,
			contentTimestamp: 0,
			isPreviewLoading: true,
			editedElement: null,
			activeElementMenu: null,
			isElementDragging: false,
			activeAddElementPopup: null,
			libraryInsertConfig: {},
		};
	},
	getters: {
		openPanels: (state): Panel[] => filter(state.panels, { isActive: true }) || [],
		isAnyPanelDragging: (state): boolean => filter(state.panels, { isDragging: true }).length > 0,
		openPanelsIDs(): string[] {
			return this.openPanels.map(panel => panel.id);
		},
		getPanel: state => {
			return (panelId: string) => state.panels.find(panel => panel.id === panelId);
		},
		getPanelPlacement: state => {
			return function (panelID: string) {
				const iframeIndex = state.panelsOrder.indexOf('preview-iframe');
				const panelIndex = state.panelsOrder.indexOf(panelID);
				return panelIndex < iframeIndex ? 'left' : 'right';
			};
		},
		getPanelOrder: state => {
			return function (panelID: string) {
				const panelIndex = state.panelsOrder.indexOf(panelID);
				return panelIndex != -1 ? panelIndex * 10 : 10;
			};
		},
		getPanelIndex: state => {
			return function (panelID: string) {
				return state.panelsOrder.indexOf(panelID);
			};
		},
	},
	actions: {
		// Element dragging
		setElementDragging(newValue: boolean) {
			this.isElementDragging = newValue;
		},
		// Preview loading
		setPreviewLoading(state: boolean) {
			this.isPreviewLoading = state;
		},
		setLoadTimestamp() {
			this.loadTimestamp = Date.now();
		},
		setContentTimestamp() {
			this.contentTimestamp = Date.now();
		},
		// Element menu
		showElementMenu(element: ZionElement, selector, actions = {}) {
			if (this.isPreviewMode) {
				return;
			}

			this.activeElementMenu = {
				element,
				selector,
				actions,
				rand: new Date().getMilliseconds(),
			};
		},
		showElementMenuFromEvent(element: ZionElement, event: MouseEvent) {
			let leftOffset = 0;
			let topOffset = 0;
			let scale = 1;

			// Check to see if the event is from iframe
			if (event.view !== window) {
				const iframe = window.frames['znpb-editor-iframe'];

				if (iframe) {
					const { left, top } = iframe.getBoundingClientRect();
					const { scaleValue } = useResponsiveDevices();
					scale = scaleValue.value / 100;

					leftOffset = left;
					topOffset = top;
				}
			}

			this.showElementMenu(element, {
				ownerDocument: window.document,
				getBoundingClientRect() {
					return {
						width: 0,
						height: 0,
						top: event.clientY * scale + topOffset,
						left: event.clientX * scale + leftOffset,
					};
				},
			});
		},
		hideElementMenu() {
			this.activeElementMenu = null;
		},

		// Element
		editElement(element: ZionElement) {
			this.editedElement = element;
			this.openPanel('panel-element-options');
		},
		unEditElement() {
			this.closePanel('panel-element-options');
			this.editedElement = null;
		},
		// Panels
		openPanel(panelId: string) {
			const panelToOpen = this.getPanel(panelId);

			if (panelToOpen) {
				// If this panel is part of a group,
				// close other panels from the same group that are already opened
				if (panelToOpen.group !== null) {
					this.openPanels.forEach(panel => {
						if (panel.group !== null && panel.group === panelToOpen.group) {
							this.closePanel(panel.id);
						}
					});
				}

				// open the panel
				panelToOpen.isActive = true;

				if (panelToOpen.saveOpenState) {
					this.saveUI();
				}
			}
		},
		closePanel(panelId: string) {
			const panel = this.getPanel(panelId);

			if (panel) {
				panel.isActive = false;

				if (panel.saveOpenState) {
					this.saveUI();
				}
			}
		},
		togglePanel(panelId: string) {
			const panel = this.getPanel(panelId);

			if (panel) {
				panel.isActive ? this.closePanel(panel.id) : this.openPanel(panel.id);
			}
		},
		updatePanel(panelId: string, key: keyof Panel, value: unknown) {
			const panel = this.getPanel(panelId);

			if (panel) {
				panel[key] = value;
			}
		},
		setPanelPlaceholder(newValue: Record<string, unknown>) {
			this.panelPlaceholder = newValue;
		},
		// Main bar
		setMainBarPosition(position: string) {
			this.mainBar.position = position;

			// Save to DB
			this.saveUI();
		},
		setIframePointerEvents(status: boolean) {
			this.iFrame.pointerEvents = status;
		},
		// Library
		openLibrary(libraryInsertConfig = {}) {
			this.isLibraryOpen = true;
			this.libraryInsertConfig = libraryInsertConfig;
		},
		closeLibrary() {
			this.isLibraryOpen = false;
			this.libraryInsertConfig = {};
		},
		toggleLibrary() {
			this.isLibraryOpen = !this.isLibraryOpen;
		},
		saveUI() {
			const { updateUserData } = useUserData();
			const uiData = {
				mainBar: {
					position: this.mainBar.position,
				},
				panels: {},
				panelsOrder: this.panelsOrder,
			};

			this.panels.forEach(panel => {
				const dataToReturn = {
					isDetached: panel.isDetached,
					offsets: panel.offsets,
					width: panel.width,
					height: panel.height,
					isActive: false,
				};

				if (panel.saveOpenState) {
					dataToReturn.isActive = panel.isActive;
				}

				uiData.panels[panel.id] = dataToReturn;
			});

			updateUserData(uiData);
		},
		setPreviewMode(state: boolean) {
			this.isPreviewMode = state;
		},

		showAddElementsPopup(element: ZionElement, event: MouseEvent, placement: 'inside' | 'next' = 'inside') {
			if (this.activeAddElementPopup && this.activeAddElementPopup.element === element) {
				this.hideAddElementsPopup();
				return;
			}

			let leftOffset = 0;
			let topOffset = 0;
			let scale = 1;

			// Check to see if the event is from iframe
			if (event.view !== window) {
				const iframe = window.frames['znpb-editor-iframe'];

				if (iframe) {
					const { left, top } = iframe.getBoundingClientRect();

					const { scaleValue } = useResponsiveDevices();
					scale = scaleValue.value / 100;
					leftOffset = left;
					topOffset = top;
				}
			}

			// Check to see if we need to insert the new element inside the provided one or the parent
			let index = -1;

			if (placement === 'next' && element.parent) {
				const elementUID = element.uid;
				index = element.parent.content.indexOf(elementUID) + 1;

				// Change the active element to the parent one
				element = element.parent;
			}

			this.activeAddElementPopup = {
				element,
				selector: {
					ownerDocument: window.document,
					getBoundingClientRect() {
						return {
							width: 0,
							height: 0,
							top: event.clientY * scale + topOffset,
							left: event.clientX * scale + leftOffset,
						};
					},
				},
				index,
				key: Math.random(),
			};
		},
		hideAddElementsPopup() {
			this.activeAddElementPopup = null;
		},
	},
});
