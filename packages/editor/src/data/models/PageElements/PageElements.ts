import Collection from "../Collection";
import PageElement from './PageElement'

export default class PageElements extends Collection {

	getModel() {
		return PageElement
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