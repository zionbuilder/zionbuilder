
let defaultOptions = {
	appendTo: 'body',
	placement: 'top'
}

export const getDefaultOptions = () => {
	return defaultOptions
}

export const setDefaults = (options) => {
	defaultOptions = options
}
