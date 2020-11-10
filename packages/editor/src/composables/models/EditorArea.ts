export class EditorArea {
	position = 'left'
	order: -999
	pointerEvents: false


	constructor(config) {
		Object.assign(this, config)
	}

	set(key, value) {
		this[key] = value
	}
}