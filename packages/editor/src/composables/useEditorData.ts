import { ref } from 'vue'

const editorData = ref(window.ZnPbInitalData)

export const useEditorData = () => {
	return {
		editorData
	}
}