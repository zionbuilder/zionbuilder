import { computed, ref, reactive } from 'vue'
import { filter, get } from 'lodash-es'

import { Panel } from './models/Panel'

import { useUserData } from './useUserData'
const { getUserData, updateUserData } = useUserData()
const UIUserData = getUserData()

// UTILS
function getPanelData(panelID: string) {
	return get(UIUserData, `panels.${panelID}`, {})
}

const defaultPanels = [
	{
		id: 'panel-element-options',
		component: 'PanelElementOptions',
		group: 'only-one',
		panelPos: 2,
		saveOpenState: false,
		...getPanelData('panel-element-options')
	},
	{
		id: 'panel-global-settings',
		component: 'panel-global-settings',
		group: 'only-one',
		panelPos: 3,
		saveOpenState: false,
		...getPanelData('panel-global-settings')
	},
	{
		id: 'panel-history',
		component: 'panel-history',
		panelPos: 19,
		...getPanelData('panel-history')
	},
	{
		id: 'panel-tree',
		component: 'panel-tree',
		panelPos: 20,
		...getPanelData('panel-tree')
	},

	{
		id: 'panel-library',
		component: 'PanelLibraryModal',
		position: 'fixed',
		width: {
			value: 1440,
			unit: 'px'
		},
		panelPos: null,
		...getPanelData('panel-library')
	}
]

// Add panel instances
const panelInstances = defaultPanels.map(panelConfig => new Panel(panelConfig))
const panels = ref(panelInstances)
const panelPlaceholder = ref({})

// Main Bar
const mainBar = reactive({
	pointerEvents: false,
	draggingPosition: null,
	...UIUserData.mainBar
})

// Iframe panel
const iFrame = reactive({
	pointerEvents: false,
	order: 6
})

export function useUI() {
	const openPanels = computed(() => {
		return filter(panels.value, {
			'isActive': true
		}) || []
	})

	const openPanelsIDs = computed(() => {
		return openPanels.value.map(panel => panel.id)
	})

	const isAnyPanelDragging = computed(() => {
		return filter(panels.value, {
			'isDragging': true
		}).length > 0
	})

	const getPanel = (panelId: string): Panel | undefined => {
		return panels.value.find(panel => panel.id === panelId)
	}

	const openPanel = (panelId: string) => {
		const panel = getPanel(panelId)

		if (panel) {
			panel.open()
		}
	}

	const closePanel = (panelId: string) => {
		const panel = getPanel(panelId)

		if (panel) {
			panel.close()
		}
	}

	const togglePanel = (panelId: string) => {
		const panel = getPanel(panelId)

		if (panel) {
			panel.toggle()
		}
	}

	const setPanelPlaceholder = (newValue) => {
		panelPlaceholder.value = newValue
	}

	// MAIN BAR
	function setMainBarPosition(position: string) {
		mainBar.position = position

		// Save to DB
		saveUI()
	}

	const getMainbarPosition = () => {
		return mainBar.position
	}

	const getMainBarPointerEvents = () => {
		return mainBar.pointerEvents
	}

	function getIframePointerEvents() {
		return iFrame.pointerEvents
	}

	function setIframePointerEvents(status: boolean) {
		iFrame.pointerEvents = status
	}

	const getIframeOrder = () => {
		return iFrame['order']
	}

	// Save to DB
	function saveUI() {
		let uiData = {
			mainBar: {
				position: mainBar.position
			},
			panels: {}
		}

		panelInstances.forEach(panel => {
			uiData.panels[panel.id] = panel.toJSON()
		});

		updateUserData(uiData)
	}

	return {
		// Panels
		openPanels,
		openPanelsIDs,
		isAnyPanelDragging,
		getPanel,
		openPanel,
		closePanel,
		togglePanel,
		panels,
		panelPlaceholder,
		setPanelPlaceholder,

		// Main bar
		mainBar,
		setMainBarPosition,
		getMainbarPosition,
		getMainBarPointerEvents,

		// Iframe
		iFrame,
		setIframePointerEvents,
		getIframePointerEvents,
		getIframeOrder,

		// Helpers
		saveUI
	}
}