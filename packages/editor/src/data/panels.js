import { Panels } from '@zb/models/panels'

export const initPanels = () => {
	return new Panels([
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
	])
}