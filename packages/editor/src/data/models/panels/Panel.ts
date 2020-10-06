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
}