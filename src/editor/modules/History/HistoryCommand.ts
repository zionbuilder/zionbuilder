import { translate } from '/@/common/modules/i18n';
import { BaseCommand } from '/@/editor/modules/Commands/BaseCommand';
import { useHistoryStore } from '/@/editor/store';

export class HistoryCommand extends BaseCommand {
	constructor(data: Record<string, unknown>) {
		super(data);
	}

	getHistory() {
		return useHistoryStore();
	}

	beforeCommand(result: unknown) {
		if (result !== null) {
			const historyStore = useHistoryStore();

			// Add the default state
			// if (historyStore.state.length === 0) {
			// 	historyStore.addHistoryItem({
			// 		commandInstance: this,
			// 		...this.getTitles(),
			// 	});
			// }

			// historyStore.addHistoryItem({
			// 	commandInstance: this,
			// 	...this.getTitles(),
			// });
		}
	}

	getTitles() {
		return {
			title: '',
			subtitle: '',
			action: '',
		};
	}

	getActionName(action: string) {
		const actions: Record<string, string> = {
			added: translate('added'),
			deleted: translate('deleted'),
		};

		return actions[action] || 'Invalid action';
	}
}
