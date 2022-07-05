export class Action {
	public name = '';
	public data: unknown = {};

	undoAction() {
		console.warn('undo action needs to be implemented by the child class');
	}

	doAction() {
		console.warn('doAction action needs to be implemented by the child class');
	}
}
