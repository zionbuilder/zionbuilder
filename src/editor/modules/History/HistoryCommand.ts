import * as i18n from '@wordpress/i18n';
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
			added: i18n.__( 'added', 'zionbuilder' ),
			deleted: i18n.__( 'deleted', 'zionbuilder' ),
			renamed: i18n.__( 'renamed', 'zionbuilder' ),
			show: i18n.__( 'show', 'zionbuilder' ),
			hide: i18n.__( 'hide', 'zionbuilder' ),
			duplicate: i18n.__( 'duplicate', 'zionbuilder' ),
			wrapped_with_container: i18n.__( 'wrapped with container', 'zionbuilder' ),
			copied: i18n.__( 'copied', 'zionbuilder' ),
			moved: i18n.__( 'moved', 'zionbuilder' ),
			pasteStyles: i18n.__( 'paste styles', 'zionbuilder' ),
			pasteCSSClasses: i18n.__( 'paste css classes', 'zionbuilder' ),
			discardStyles: i18n.__( 'discard styles', 'zionbuilder' ),
		};

		return actions[action] || 'Invalid action';
	}
}
