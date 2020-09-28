import optionsManager from './index'

const options = optionsManager()

export const {
	registerOption,
	getOptionComponent,
	getOption,
	removeActiveResponsiveOptions,
	getActiveResponsiveOptions,
	setActiveResponsiveOptions
} = options