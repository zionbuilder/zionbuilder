import { ref } from 'vue';
import { useUIStore, useContentStore } from '../../store';

export function useTreeViewList() {
	const elementOptionsRef = ref(null);
	const UIStore = useUIStore();
	const contentStore = useContentStore();

	function sortableStart() {
		UIStore.setElementDragging(true);
	}

	function sortableEnd() {
		UIStore.setElementDragging(false);
	}

	function onSortableDrop(event) {
		const { item, to, newIndex, duplicateItem, placeBefore } = event.data;

		const movedElement = contentStore.getElement(item.dataset.zionElementUid);
		if (duplicateItem) {
			const elementForInsert = movedElement.getClone();
			window.zb.run('editor/elements/add', {
				parentUID: to.dataset.zionElementUid,
				element: elementForInsert,
				index: placeBefore ? newIndex : newIndex + 1,
			});
		} else {
			window.zb.run('editor/elements/move', {
				newParent: contentStore.getElement(to.dataset.zionElementUid),
				element: contentStore.getElement(item.dataset.zionElementUid),
				index: newIndex,
			});
		}
	}

	return {
		elementOptionsRef,
		sortableStart,
		sortableEnd,
		onSortableDrop,
	};
}
