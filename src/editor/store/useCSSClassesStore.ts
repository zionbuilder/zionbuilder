import { defineStore } from 'pinia';
import { cloneDeep, merge } from 'lodash-es';

const { generateUID } = window.zb.utils;

type CssStyles = Record<string, unknown>;

type CSSClass = {
	id: string;
	name: string;
	uid: string;
	styles: CssStyles;
};

export const useCSSClassesStore = defineStore('CSSClasses', {
	state: (): {
		CSSClasses: CSSClass[];
		copiedStyles: CssStyles | null;
	} => {
		return {
			CSSClasses: [],
			copiedStyles: null,
		};
	},
	getters: {
		getClassesByFilter: state => {
			return (keyword: string): CSSClass[] => {
				const keyToLower = keyword.toLowerCase();

				return state.CSSClasses.filter(
					cssClass =>
						cssClass.name.toLowerCase().indexOf(keyToLower) !== -1 ||
						cssClass.id.toLowerCase().indexOf(keyToLower) !== -1,
				);
			};
		},
		getClassConfig: state => {
			/**
			 * Get the class config by id. It first checks for the uid and then for the id for backwards compatibility
			 *
			 * @param state
			 */
			return (classId: string) =>
				state.CSSClasses.find(classConfig => classConfig.uid === classId || classConfig.id === classId);
		},
		getSelectorName() {
			return (class_uid_or_selector: string) => {
				const config = this.getClassConfig(class_uid_or_selector);
				if (config) {
					return config.id;
				}

				return null;
			};
		},
		getStylesConfig() {
			return (classId: string) => {
				const config = this.getClassConfig(classId);

				if (!config) {
					alert(`class with id ${classId} not found`);
				}

				return config.styles || {};
			};
		},
	},
	actions: {
		addCSSClass(config) {
			const classToAdd = { ...config };
			classToAdd.uid = config.uid || generateUID();
			this.CSSClasses.push(classToAdd);
		},
		removeCSSClass(cssClass) {
			const cssClassIndex = this.CSSClasses.indexOf(cssClass);
			this.CSSClasses.splice(cssClassIndex, 1);
		},
		updateCSSClass(classId: string, newValues) {
			const editedClass = this.CSSClasses.find(cssClassConfig => {
				return cssClassConfig.id === classId;
			});
			if (!editedClass) {
				// eslint-disable-next-line
				console.warn('could not find class with config ', { classId, newValues })
				return;
			}
			const cssClassIndex = this.CSSClasses.indexOf(editedClass);
			const updatedValues = {
				...editedClass,
				...newValues,
			};
			this.CSSClasses[cssClassIndex] = updatedValues;
		},
		removeAllCssClasses() {
			this.CSSClasses = [];
		},
		setCSSClasses(newValue) {
			this.CSSClasses = newValue;
		},
		copyClassStyles(styles: CssStyles) {
			console.log(styles);
			this.copiedStyles = cloneDeep(styles);
		},
		pasteClassStyles(classId: string) {
			const oldStyles = this.getStylesConfig(classId);
			const mergedStyles = merge(oldStyles || {}, cloneDeep(this.copiedStyles));
			this.updateCSSClass(classId, {
				styles: mergedStyles,
			});
		},
	},
});
