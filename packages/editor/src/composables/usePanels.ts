import { computed, ref } from 'vue'
import { filter } from 'lodash-es'
import { Panel } from './models'

const defaultPanels = [
	{
		id: 'PanelElementOptions',
		group: 'only-one',
		panelPos: 2
	},
	{
		id: 'panel-global-settings',
		group: 'only-one',
		panelPos: 3
	},
	{
		id: 'panel-tree',
		panelPos: 4
	},
	{
		id: 'panel-history',
		panelPos: 5
	},
	{
		id: 'PanelLibraryModal',
		position: 'fixed',
		width: {
			value: 1440,
			unit: 'px'
		},
		panelPos: null
	}
]

// Add panel instances
const panelInstances = defaultPanels.map(panelConfig => new Panel(panelConfig))

const panels = ref(panelInstances)
const panelPlaceholder = ref({})

export function usePanels () {
	const openPanels = computed(() => {
		return filter(panels.value, {
			'isActive': true
		}) || []
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

	return {
		openPanels,
		isAnyPanelDragging,
		getPanel,
		openPanel,
		closePanel,
		togglePanel,
		panels,
		panelPlaceholder,
		setPanelPlaceholder
	}
}