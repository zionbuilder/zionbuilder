import { useUI } from '../useUI'

export class Panel {
	id = ''
	position = 'relative'
	isDetached = false
	isDragging = false
	isExpanded = false
	isActive = false
	width = {
		value: 360,
		unit: 'px'
	}
	height = {
		value: null,
		unit: 'auto'
	}
	panelPos = 1
	group = null
	saveOpenState: boolean = true

	constructor(config) {
		Object.assign(this, config)
	}

	set(key, value) {
		this[key] = value
	}

	close() {
		const { saveUI } = useUI()

		this.isActive = false

		if (this.saveOpenState) {
			saveUI()
		}
	}

	open() {
		this.isActive = true
		const { openPanels, saveUI } = useUI()

		// If this panel is part of a group,
		// close other panels from the same group that are already opened
		if (this.group !== null) {
			openPanels.value.forEach(panel => {
				if (panel.group !== null && panel.group === this.group && panel !== this) {
					panel.close()
				}
			})
		}

		if (this.saveOpenState) {
			saveUI()
		}
	}

	toggle() {
		console.log('toggle');
		this.isActive ? this.close() : this.open()
	}

	toJSON() {
		const dataToReturn = {
			panelPos: this.panelPos,
			isDetached: this.isDetached,
			width: this.width,
			height: this.height
		}

		if (this.saveOpenState) {
			dataToReturn.isActive = this.isActive
		}

		return dataToReturn
	}
}