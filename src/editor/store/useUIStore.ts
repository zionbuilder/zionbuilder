import { defineStore } from 'pinia';
import { filter, get } from 'lodash-es';

import { Panel } from '../models/Panel';
import { useUserData } from '../composables/useUserData';

export const useUIStore = defineStore('ui', {
	state: () => {
		const { getUserData } = useUserData();
		const UIUserData = getUserData();

		const defaultPanels = [
			{
				id: 'panel-element-options',
				component: 'PanelElementOptions',
				saveOpenState: false,
				...getPanelData('panel-element-options'),
			},
			{
				id: 'panel-global-settings',
				component: 'panel-global-settings',
				saveOpenState: false,
				...getPanelData('panel-global-settings'),
			},
			{
				id: 'preview-iframe',
				component: 'PreviewIframe',
				isActive: true,
			},
			{
				id: 'panel-history',
				component: 'panel-history',
				...getPanelData('panel-history'),
			},
			{
				id: 'panel-tree',
				component: 'panel-tree',
				...getPanelData('panel-tree'),
			},
		];
		const panelInstances = defaultPanels.map(panelConfig => new Panel(panelConfig));

		function getPanelData(panelID: string) {
			return get(UIUserData, `panels.${panelID}`, {});
		}

		return {
			panelsOrder: get(UIUserData, 'panelsOrder', [
				'panel-element-options',
				'panel-global-settings',
				'preview-iframe',
				'panel-history',
				'panel-tree',
			]),
			panelPlaceholder: {},
			panels: panelInstances,
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
		};
	},
	getters: {
		openPanels: state => filter(state.panels, { isActive: true }) || [],
		isAnyPanelDragging: state => filter(state.panels, { isDragging: true }).length > 0,
		openPanelsIDs() {
			return this.openPanels.map(panel => panel.id);
		},
		getPanel: state => {
			return (panelId: string): Panel | undefined => state.panels.find(panel => panel.id === panelId);
		},
		getPanelPlacement: state => {
			return function (panelId: string) {
				const iframeIndex = state.panelsOrder.indexOf('preview-iframe');
				const panelIndex = state.panelsOrder.indexOf(panelId);

				return panelIndex < iframeIndex ? 'left' : 'right';
			};
		},
		getPanelOrder: state => {
			return function (panelId: string) {
				const panelIndex = state.panelsOrder.indexOf(panelId);
				return panelIndex != -1 ? panelIndex * 10 : 10;
			};
		},
	},
	actions: {
		openPanel(panelId: string) {
			const panel = this.getPanel(panelId);

			if (panel) {
				panel.open();
			}
		},
		closePanel(panelId: string) {
			const panel = this.getPanel(panelId);

			if (panel) {
				panel.close();
			}
		},
		togglePanel(panelId: string) {
			const panel = this.getPanel(panelId);

			if (panel) {
				panel.toggle();
			}
		},
		setPanelPlaceholder(newValue) {
			this.panelPlaceholder = newValue;
		},
		setMainBarPosition(position: string) {
			this.mainBar.position = position;

			// Save to DB
			this.saveUI();
		},
		setIframePointerEvents(status: boolean) {
			this.iFrame.pointerEvents = status;
		},
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
				uiData.panels[panel.id] = panel.toJSON();
			});

			updateUserData(uiData);
		},
	},
});
