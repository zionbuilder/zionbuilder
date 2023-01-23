import { defineStore } from 'pinia';
import { getGoogleFonts } from '../api';

export type GoogleFont = {
	family: string;
	variants: string[];
	subsets: string[];
	version: string;
	lastModified: string;
	files: {
		[key: string]: string;
	};
	category: string;
};

export const useGoogleFontsStore = defineStore('googleFonts', {
	state: () => {
		return {
			// all these properties will have their type inferred automatically
			isLoading: false,
			fetched: false,
			fonts: [],
		};
	},
	getters: {
		getFontData: (state) => {
			return (family: string): GoogleFont | undefined => state.fonts.find(font => font['family'] == family);
		},
	},
	actions: {
		fetchGoogleFonts(force = false) {
			// Don't refetch the options if  they were already fetched
			if (this.fetched && !force) {
				return Promise.resolve(this.fonts);
			}

			return getGoogleFonts().then(response => {
				this.fonts = response.data;
			});
		},
	},
});
