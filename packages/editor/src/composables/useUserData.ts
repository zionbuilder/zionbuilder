import { reactive } from 'vue'
import { get } from 'lodash-es'
import { saveUserData } from '@zb/rest'
import { useEditorData } from './useEditorData'

const { editorData } = useEditorData()

let userDataValues = editorData.value.user_data || {
	ui_data: {
		mainBar: {
			position: 'left'
		},
		panels: {}
	}
}

export function useUserData() {
	function getUserData() {
		return userDataValues
	}

	function updateUserData(newData: Object) {
		saveUserData(newData).then((response) => {
			userDataValues = response.data
		})
	}

	return {
		getUserData,
		updateUserData
	}
}