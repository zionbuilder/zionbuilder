import { __ } from '@wordpress/i18n';
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
			added: __( 'added', 'zionbuilder' ),
			deleted: __( 'deleted', 'zionbuilder' ),
			renamed: __( 'renamed', 'zionbuilder' ),
			show: __( 'show', 'zionbuilder' ),
			hide: __( 'hide', 'zionbuilder' ),
			duplicate: __( 'duplicate', 'zionbuilder' ),
			wrapped_with_container: __( 'wrapped with container', 'zionbuilder' ),
			copied: __( 'copied', 'zionbuilder' ),
			moved: __( 'moved', 'zionbuilder' ),
			pasteStyles: __( 'paste styles', 'zionbuilder' ),
			pasteCSSClasses: __( 'paste css classes', 'zionbuilder' ),
			discardStyles: __( 'discard styles', 'zionbuilder' ),
		};

		return actions[action] || 'Invalid action';
	}
}
