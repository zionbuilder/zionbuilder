import { ref, Ref } from 'vue';

export class HistoryManager {
	private state: Action[] = ref([]);

	constructor() {}
}
