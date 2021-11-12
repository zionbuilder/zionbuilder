import { merge } from 'lodash-es'
import { saveUserData } from '@zb/rest'
import { useEditorData } from './useEditorData'

const { editorData } = useEditorData()

let userDataValues = merge({}, {
	ui_data: {
		mainBar: {
			position: 'left'
		},
		panels: {}
	},
	...editorData.value.user_data
})

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