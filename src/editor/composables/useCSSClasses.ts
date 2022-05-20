import { ref } from 'vue';
import { cloneDeep, merge } from 'lodash-es';
import { generateUID } from '@common/utils';

const CSSClasses = ref(window.ZnPbInitalData.css_classes || []);
const copiedStyles = ref(null);

export const useCSSClasses = () => {
	const getClassesByFilter = keyword => {
		const keyToLower = keyword.toLowerCase();
		return CSSClasses.value.filter(
			className =>
				className.name.toLowerCase().indexOf(keyToLower) !== -1 ||
				className.id.toLowerCase().indexOf(keyToLower) !== -1,
		);
	};

	const getClassConfig = classId => {
		return CSSClasses.value.find(classConfig => {
			return classConfig.id === classId;
		});
	};

	const addCSSClass = config => {
		const classToAdd = { ...config };
		classToAdd.uid = config.uid || generateUID();
		CSSClasses.value.push(classToAdd);
	};

	const removeCSSClass = cssClass => {
		const cssClassIndex = CSSClasses.value.indexOf(cssClass);
		CSSClasses.value.splice(cssClassIndex, 1);
	};

	const updateCSSClass = (classId, newValues) => {
		const editedClass = CSSClasses.value.find(cssClassConfig => {
			return cssClassConfig.id === classId;
		});

		if (!editedClass) {
			// eslint-disable-next-line
			console.warn('could not find class with config ', { classId, newValues })
			return;
		}

		const cssClassIndex = CSSClasses.value.indexOf(editedClass);
		const updatedValues = {
			...editedClass,
			...newValues,
		};

		CSSClasses.value[cssClassIndex] = updatedValues;
	};

	function removeAllCssClasses() {
		CSSClasses.value = [];
	}

	function setCSSClasses(newValue) {
		CSSClasses.value = newValue;
	}

	// copy/paste styles
	function getStylesConfig(classId) {
		const config = getClassConfig(classId);

		if (!config) {
			alert(`class with id ${classId} not found`);
		}

		return config.styles || {};
	}

	function copyClassStyles(styles) {
		copiedStyles.value = cloneDeep(styles);
	}

	function pasteClassStyles(classId) {
		const oldStyles = getStylesConfig(classId);
		const mergedStyles = merge(oldStyles || {}, cloneDeep(copiedStyles.value));

		updateCSSClass(classId, {
			styles: mergedStyles,
		});
	}

	return {
		copiedStyles,
		CSSClasses,
		getClassesByFilter,
		getClassConfig,
		addCSSClass,
		removeCSSClass,
		updateCSSClass,
		removeAllCssClasses,
		setCSSClasses,
		copyClassStyles,
		getStylesConfig,
		pasteClassStyles,
	};
};
