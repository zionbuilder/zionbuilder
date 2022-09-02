export class Panel {
	id = '';
	position = 'relative';
	isDetached = false;
	isDragging = false;
	isExpanded = false;
	isActive = false;
	width = 360;
	height = null;
	group = null;
	saveOpenState = true;
	offsets = {
		posX: null,
		posY: null,
	};

	constructor(config) {
		Object.assign(this, config);
	}
}
