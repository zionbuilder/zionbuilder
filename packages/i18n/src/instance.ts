import { createI18n } from './manager'

const i18n = createI18n()

export const install = (app, strings: object) => {
	// Add the strings
	i18n.addStrings(strings)

	// Add helper method
	app.config.globalProperties.$translate = (string: string) => {
		return i18n.translate(string)
	}
}

export const { addStrings, translate } = i18n