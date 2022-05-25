import { defineStore } from 'pinia';
import { filter, get } from 'lodash-es';
import { useUserData } from '../composables/useUserData';

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
	state: () => {
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
				component: 'PanelElementOptions',
				saveOpenState: false,
			}),
			getPanelData('panel-global-settings', {
				component: 'panel-global-settings',
				saveOpenState: false,
			}),
			getPanelData('preview-iframe', {
				component: 'PreviewIframe',
				isActive: true,
			}),
			getPanelData('panel-history', {
				component: 'panel-history',
			}),
			getPanelData('panel-tree', {
				component: 'panel-tree',
			}),
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
		openLibrary() {
			this.isLibraryOpen = true;
		},
		closeLibrary() {
			this.isLibraryOpen = false;
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
	},
});
