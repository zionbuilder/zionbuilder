import { useUI } from '../useUI'

export class Panel {
	id = ''
	position = 'relative'
	isDetached = false
	isDragging = false
	isExpanded = false
	isActive = false
	width = 360
	height = 'auto'
	panelPos = 1
	group = null
	saveOpenState: boolean = true
	detachedPosition = {}
	offsets = {
		posX: null,
		posY: null,
	}

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
			offsets: this.offsets,
			width: this.width,
			height: this.height,
			detachedPosition: this.detachedPosition
		}

		if (this.saveOpenState) {
			dataToReturn.isActive = this.isActive
		}

		return dataToReturn
	}
}