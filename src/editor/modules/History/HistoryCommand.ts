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

	getActionName(action: string) {
		const actions: Record<string, string> = {
			added: translate('added'),
			deleted: translate('deleted'),
			renamed: translate('renamed'),
			show: translate('show'),
			hide: translate('hide'),
		};

		return actions[action] || 'Invalid action';
	}
}