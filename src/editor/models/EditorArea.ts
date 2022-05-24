export class EditorArea {
	position = 'left';
	pointerEvents: false;

	constructor(config) {
		Object.assign(this, config);
	}

	set(key, value) {
		this[key] = value;
	}
}
