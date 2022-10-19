import { get, set, unset } from 'lodash-es';

const localStorageKey = 'zionbuilder';

export function useLocalStorage() {
	function getStorageData() {
		const savedData = localStorage.getItem(localStorageKey);
		return savedData !== null ? JSON.parse(savedData) : {};
	}

	function addData(path: string, value: any) {
		const storageData = getStorageData();
		// Set the new data
		set(storageData, path, value);

		localStorage.setItem(localStorageKey, JSON.stringify(storageData));
	}

	function getData(path: string, defaultValue = null) {
		const storageData = getStorageData();
		return get(storageData, path, defaultValue);
	}

	function removeData(path: string) {
		const storageData = getStorageData();
		unset(storageData, path);

		localStorage.setItem(localStorageKey, JSON.stringify(storageData));
	}

	return {
		addData,
		getData,
		removeData,
	};
}
