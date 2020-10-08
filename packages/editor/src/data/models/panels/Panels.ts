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
			panel.open()
		}
	}

	closePanel (panelId: string) {
		const panel = this.find({id: panelId})

		if (panel) {
			panel.close()
		}
	}

	togglePanel (panelId: string) {
		const panel = this.find({id: panelId})

		if (panel) {
			panel.toggle()
		}
	}

	getPanel (panelId: string) {
		return this.find({
			'id': panelId
		})
	}
}