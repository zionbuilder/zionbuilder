import { useUIStore } from '../store';
import { storeToRefs } from 'pinia';
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
	detachedPosition = {};
	offsets = {
		posX: null,
		posY: null,
	};

	constructor(config) {
		Object.assign(this, config);
	}

	get placement(): string {
		const { getPanelPlacement } = useUIStore();
		return getPanelPlacement(this.id);
	}

	get order(): number {
		const { getPanelOrder } = useUIStore();
		return getPanelOrder(this.id);
	}

	get index() {
		const { panelsOrder } = storeToRefs(useUIStore());
		return panelsOrder.value.indexOf(this.id);
	}

	set(key, value) {
		this[key] = value;
	}

	close() {
		const { saveUI } = useUIStore();

		this.isActive = false;

		if (this.saveOpenState) {
			saveUI();
		}
	}

	open() {
		this.isActive = true;
		const { openPanels, saveUI } = useUIStore();

		// If this panel is part of a group,
		// close other panels from the same group that are already opened
		if (this.group !== null) {
			openPanels.value.forEach(panel => {
				if (panel.group !== null && panel.group === this.group && panel !== this) {
					panel.close();
				}
			});
		}

		if (this.saveOpenState) {
			saveUI();
		}
	}

	toggle() {
		this.isActive ? this.close() : this.open();
	}

	toJSON() {
		const dataToReturn = {
			isDetached: this.isDetached,
			offsets: this.offsets,
			width: this.width,
			height: this.height,
			detachedPosition: this.detachedPosition,
		};

		if (this.saveOpenState) {
			dataToReturn.isActive = this.isActive;
		}

		return dataToReturn;
	}
}
