export class BaseCommand {
	public data: unknown = {};

	constructor(data: Record<string, unknown>) {
		this.data = data;
	}

	runCommand() {
		this.beforeCommand();
		const result = this.doCommand();
		this.afterCommand(result);

		return result;
	}

	undoCommand() {
		console.warn('undoCommand needs to be implemented by the child class');
	}

	doCommand() {
		console.warn('doCommand needs to be implemented by the child class');
	}

	beforeCommand() {
		// Do nothing. This should be implemented by child classes
	}

	afterCommand(result: unknown) {
		// Do nothing. This should be implemented by child classes
	}
}
