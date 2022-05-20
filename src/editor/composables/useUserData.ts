import { reactive } from 'vue';
import { saveUserData } from '@common/api';
import { useEditorData } from './useEditorData';

const { editorData } = useEditorData();

const userDataValues = reactive({
	favorite_elements: [],
	...editorData.value.user_data,
});

export function useUserData() {
	function getUserData(key = null, defaultValue = null) {
		if (key !== null) {
			return userDataValues[key] || defaultValue;
		}

		return userDataValues;
	}

	function updateUserData(newData: Object) {
		const dataToSave = {
			...userDataValues.value,
			...newData,
		};

		// Imediately save the new data so we can update the UI
		Object.assign(userDataValues, dataToSave);

		saveUserData(dataToSave).then(response => {
			// Keep reactivity
			Object.assign(userDataValues, response.data);
		});
	}

	return {
		getUserData,
		updateUserData,
	};
}
