import { ref, Ref } from 'vue';

const isPreviewLoading: Ref<boolean> = ref(true);
const loadTimestamp = ref(null);
const contentTimestamp = ref(null);

export function usePreviewLoading() {
	const setPreviewLoading = (state: boolean) => {
		isPreviewLoading.value = state;
	};

	function setLoadTimestamp() {
		loadTimestamp.value = Date.now();
	}

	function setContentTimestamp() {
		contentTimestamp.value = Date.now();
	}

	return {
		setPreviewLoading,
		isPreviewLoading,
		loadTimestamp,
		contentTimestamp,
		setLoadTimestamp,
		setContentTimestamp,
	};
}
