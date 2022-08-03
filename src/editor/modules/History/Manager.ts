import * as commands from './Commands';
import { ref } from 'vue';

type Transaction = {
	do: Function;
	undo: Function;
	title: string;
	subtitle: string;
	action: string;
};

export class HistoryManager {
	private state: Transaction[] = ref([]);

	constructor() {
		console.log('asdasdad');
		// Register commands
		Object.keys(commands).forEach(importId => {
			const classObject = commands[importId];
			if (classObject.commandID) {
				window.zb.commandsManager.registerCommand(classObject.commandID, classObject);
			}
		});
	}

	addTransaction(transaction: Transaction) {
		this.state.value.push(transaction);
	}

	undo() {}

	redo() {}
}
