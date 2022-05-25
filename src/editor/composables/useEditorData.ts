import { ref } from 'vue';

// TODO: @vite - find a better solution
const editorData = ref(window.ZnPbInitialData || window.ZnPbPreviewData);

export const useEditorData = () => {
	return {
		editorData,
	};
};
