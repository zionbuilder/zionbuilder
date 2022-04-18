import { createI18n } from './manager';
import { App } from 'vue';

const i18n = createI18n();

export const install = (app: App, strings: translateString) => {
	// Add the strings
	i18n.addStrings(strings);

	// Add helper method
	app.config.globalProperties.$translate = (string: string) => {
		return i18n.translate(string);
	};
};

export const { addStrings, translate } = i18n;
