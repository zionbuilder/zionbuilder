import { each } from 'lodash-es'

const windows: {[key: string]: Window } = {
	main: window
}

export const useWindows = () => {

	const getWindows = (windowID = null) => {
		return windowID ? windows[windowID] : windows
	}

	const addWindow = (id: string, document: Window) => {
		windows[id] = document
	}

	const addEventListener = (type: string, callback, options) => {
		each(windows, doc => {
			doc.addEventListener(type, callback, options)
		})
	}

	const removeWindow = (id: string) => {
		delete windows[id]
	}

	const removeEventListener = (type: string, callback, options) => {
		each(windows, doc => {
			doc.removeEventListener(type, callback, options)
		})
	}

	return {
		getWindows,
		addWindow,
		removeWindow,
		addEventListener,
		removeEventListener
	}
}