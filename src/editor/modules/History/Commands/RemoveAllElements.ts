import { HistoryCommand } from '../HistoryCommand';
import { translate } from '/@/common/modules/i18n';
import { useContentStore } from '/@/editor/store';

export class RemoveAllElements extends HistoryCommand {
	static commandID = 'editor/elements/remove_all';

	doCommand() {
		const contentStore = useContentStore();
		const { areaID } = this.data;
		const areaElement = contentStore.getElement(areaID);
		let areaModel = {};

		if (areaElement) {
			// save the model
			areaModel = areaElement?.toJSON();
			console.log(areaElement, areaModel);

			// clear area content
			contentStore.clearAreaContent(areaID);

			const historyManager = this.getHistory();

			// Add to history
			historyManager.addHistoryItem({
				undo: RemoveAllElements.undo,
				redo: RemoveAllElements.redo,
				data: {
					areaID,
					areaModel,
				},
				title: translate('page_cleared'),
			});
		}
	}

	public static undo(historyItem) {
		const { areaID, areaModel } = historyItem.data || {};
		const contentStore = useContentStore();

		contentStore.registerArea(
			{
				name: areaID,
				id: areaID,
			},
			areaModel.content,
		);
	}

	public static redo(historyItem) {
		const { areaID, areaModel } = historyItem.data || {};

		const contentStore = useContentStore();

		// clear area content
		contentStore.clearAreaContent(areaID);
	}
}
