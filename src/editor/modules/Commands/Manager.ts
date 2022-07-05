export class CommandManager {
	private commands = [];

	constructor() {}

	registerCommand() {}

	runCommand(commandName: string, commandArgs: Record<string, unknown>) {
		const command = this.commands.find(command => (command.id = commandName));

		if (command) {
			new command.Object();
		}
	}
}
