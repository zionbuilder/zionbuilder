import { ref, reactive, computed } from 'vue';
import { defineStore } from 'pinia';
import { filter, get } from 'lodash-es';

import { Panel } from '../models/Panel';
import { EditorArea } from '../models/EditorArea';

import { useUserData } from '../composables/useUserData';

export const useUIStore = defineStore('ui', () => {
	const { getUserData, updateUserData } = useUserData();
	const UIUserData = getUserData();

	// UTILS
	function getPanelData(panelID: string) {
		return get(UIUserData, `panels.${panelID}`, {});
	}

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

	const panelsOrder = ref(
		get(UIUserData, 'panelsOrder', [
			'panel-element-options',
			'panel-global-settings',
			'preview-iframe',
			'panel-history',
			'panel-tree',
		]),
	);

	// Add panel instances
	const panelInstances = defaultPanels.map(panelConfig => new Panel(panelConfig));
	const panels = ref(panelInstances);
	const panelPlaceholder = ref({});

	// Main Bar
	const mainBar = reactive(
		new EditorArea({
			position: 'left',
			pointerEvents: false,
			draggingPosition: null,
			...(UIUserData.mainBar || {}),
		}),
	);

	const mainBarDraggingPlaceholder = reactive({
		top: null,
		left: null,
	});

	// Iframe panel
	const iFrame = reactive(
		new EditorArea({
			pointerEvents: false,
		}),
	);

	const isLibraryOpen = ref(false);

	const openPanels = computed(() => {
		return (
			filter(panels.value, {
				isActive: true,
			}) || []
		);
	});

	const openPanelsIDs = computed(() => {
		return openPanels.value.map(panel => panel.id);
	});

	const isAnyPanelDragging = computed(() => {
		return (
			filter(panels.value, {
				isDragging: true,
			}).length > 0
		);
	});

	const getPanel = (panelId: string): Panel | undefined => {
		return panels.value.find(panel => panel.id === panelId);
	};

	const openPanel = (panelId: string) => {
		const panel = getPanel(panelId);

		if (panel) {
			panel.open();
		}
	};

	const closePanel = (panelId: string) => {
		const panel = getPanel(panelId);

		if (panel) {
			panel.close();
		}
	};

	function getPanelPlacement(panelID: string) {
		const iframeIndex = panelsOrder.value.indexOf('preview-iframe');
		const panelIndex = panelsOrder.value.indexOf(panelID);

		return panelIndex < iframeIndex ? 'left' : 'right';
	}

	function getPanelOrder(panelID: string) {
		const panelIndex = panelsOrder.value.indexOf(panelID);
		return panelIndex != -1 ? panelIndex * 10 : 10;
	}

	const togglePanel = (panelId: string) => {
		const panel = getPanel(panelId);

		if (panel) {
			panel.toggle();
		}
	};

	const setPanelPlaceholder = newValue => {
		panelPlaceholder.value = newValue;
	};

	// MAIN BAR
	function setMainBarPosition(position: string) {
		mainBar.position = position;

		// Save to DB
		saveUI();
	}

	const getMainbarPosition = () => {
		return mainBar.position;
	};

	const getMainBarPointerEvents = () => {
		return mainBar.pointerEvents;
	};

	function getIframePointerEvents() {
		return iFrame.pointerEvents;
	}

	function setIframePointerEvents(status: boolean) {
		iFrame.pointerEvents = status;
	}

	// Save to DB
	function saveUI() {
		const uiData = {
			mainBar: {
				position: mainBar.position,
			},
			panels: {},
			panelsOrder: panelsOrder.value,
		};

		panelInstances.forEach(panel => {
			uiData.panels[panel.id] = panel.toJSON();
		});

		updateUserData(uiData);
	}

	// LIBRARY
	function openLibrary() {
		isLibraryOpen.value = true;
	}

	function closeLibrary() {
		isLibraryOpen.value = false;
	}

	function toggleLibrary() {
		isLibraryOpen.value = !isLibraryOpen.value;
	}

	return {
		// Library
		isLibraryOpen,
		openLibrary,
		closeLibrary,
		toggleLibrary,
		// Panels
		panelsOrder,
		openPanels,
		openPanelsIDs,
		isAnyPanelDragging,
		getPanelPlacement,
		getPanelOrder,
		getPanel,
		openPanel,
		closePanel,
		togglePanel,
		panels,
		panelPlaceholder,
		setPanelPlaceholder,

		// Main bar
		mainBar,
		mainBarDraggingPlaceholder,
		setMainBarPosition,
		getMainbarPosition,
		getMainBarPointerEvents,

		// Iframe
		iFrame,
		setIframePointerEvents,
		getIframePointerEvents,

		// Helpers
		saveUI,
	};
});
