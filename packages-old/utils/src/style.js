/**
 * DEPRECATED. Will be removed in later versions
 */
export function getCssFromSelector(selectors, styleConfig, args = {}) {
	console.warn('This was deprecated in favor of zb.editor.utill.getCssFromSelector');

	return window.zb.editor.utill.getCssFromSelector(selectors, styleConfig, args);
}

export function getStyles(cssSelector, styleValues = {}, args) {
	console.warn('This was deprecated in favor of zb.editor.utill.getStyles');

	return window.zb.editor.utill.getStyles(cssSelector, styleValues, args);
}

export function getPseudoStyles(cssSelector, pseudoSelectors = {}, args) {
	console.warn('This was deprecated in favor of zb.editor.utill.getPseudoStyles');

	return window.zb.editor.utill.getPseudoStyles(cssSelector, pseudoSelectors, args);
}

export function getResponsiveDeviceStyles(deviceId, styles) {
	console.warn('This was deprecated in favor of zb.editor.utill.getResponsiveDeviceStyles');

	return window.zb.editor.utill.getResponsiveDeviceStyles(deviceId, styles);
}

export function compileStyleTabs(styleValues) {
	console.warn('This was deprecated in favor of zb.editor.utill.compileStyleTabs');

	return window.zb.editor.utill.compileStyleTabs(styleValues);
}

export function getGradientCss(config) {
	console.warn('This was deprecated in favor of zb.editor.utill.getGradientCss');

	return window.zb.editor.utill.getGradientCss(config);
}

export function compileFontTab(styleValues) {
	console.warn('This was deprecated in favor of zb.editor.utill.compileFontTab');

	return window.zb.editor.utill.compileFontTab(styleValues);
}
