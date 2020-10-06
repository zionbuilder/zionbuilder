import Collection from "../Collection";
import Panel from './Panel'

export default class Panels extends Collection {

	getModel() {
		return Panel
	}

	get openPanels () {
		return this.filter({
			'isActive': true
		}) || []
	}

	get isAnyPanelDragging () {
		return this.filter({
			'isDragging': true
		}).length > 0
	}

	openPanel (panelId: string) {
		const panel = this.find({id: panelId})

		if (panel) {
			panel.set('isActive', true)
		}
	}
	togglePanel (panelId: string) {
		const panel = this.find({id: panelId})

		if (panel) {
			panel.set('isActive', !panel.isActive)
		}
	}

	getPanel (panelId: string) {
		return this.find({
			'id': panelId
		})
	}
}