import { BaseCommand } from './BaseCommand';

type Command = {
	id: string;
	callback: Function;
};

export class CommandManager {
	private commands: Command[] = [];

	registerCommand(commandId: string, commandClass: typeof BaseCommand) {
		this.commands.push({
			id: commandId,
			callback: (commandArgs: Record<string, unknown>) => {
				return new commandClass(commandArgs).runCommand();
			},
		});
	}

	getCommand(commandId: string): Command | false {
		const command = this.commands.find(command => command.id === commandId);

		if (!command) {
			console.warn(`Command ${commandId} not found`);

			return false;
		}

		return command;
	}

	runCommand(commandId: string, commandArgs: Record<string, unknown>) {
		const command = this.getCommand(commandId);
		if (command) {
			return command.callback.call(command, commandArgs);
		} else {
			console.warn(`Command with id ${commandId} not found`);
		}

		return null;
	}
}
