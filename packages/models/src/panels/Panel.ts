import { Panels } from '.'
import Model from '../Model'

export default class Panel extends Model {
	defaults () {
		return {
			position: 'relative',
			isDetached: false,
			isDragging: false,
			isExpanded: false,
			isActive: false,
			background: '#302c36',
			width: {
				value: 360,
				unit: 'px'
			},
			height: {
				value: null,
				unit: 'auto'
			},
			panelPos: 1
		}
	}

	close () {
		this.set('isActive', false)
	}

	open () {
		this.set('isActive', true)

		// If this panel is part of a group,
		// close other panels from the same group that are already opened
		if (this.group !== 'undefined') {
			(<Panels>this.getCollection()).openPanels.forEach(panel => {
				if (typeof panel.group !== 'undefined' && panel.group === this.group && panel !== this.panel) {
					panel.close()
				}
			})
		}
	}

	toggle () {
		this.set('isActive', !this.isActive)
	}
}