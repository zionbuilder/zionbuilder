import * as I18n from './i18n'

const i18n = I18n.createI18n()

export const install = (app, strings: object) => {
	// Add the strings
	i18n.addStrings(strings)

	// Add helper method
	app.config.globalProperties.$translate = (string: string) => {
		return i18n.translate(string)
	}
}

export const addStrings = i18n.addStrings
export const translate = i18n.translate