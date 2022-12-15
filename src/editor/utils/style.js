const { useResponsiveDevices } = window.zb.composables;

export function getCssFromSelector(selectors, styleConfig, args = {}) {
	let css = '';

	if (styleConfig.styles) {
		css += getStyles(selectors.join(','), styleConfig.styles, args);
	}

	// Check for child classes
	if (styleConfig.child_styles) {
		styleConfig.child_styles.forEach(childConfig => {
			const { states = ['default'], selector, styles } = childConfig;

			// Check for nested selectors
			const childSelectors = [];
			selectors.forEach(mainSelector => {
				states.forEach(state => {
					if (state === 'default') {
						childSelectors.push(`${mainSelector} ${selector}`);
					} else {
						childSelectors.push(`${mainSelector}${state} ${selector}`);
					}
				});
			});

			css += getCssFromSelector(childSelectors, childConfig, args);
		});
	}

	return css;
}

export function getStyles(cssSelector, styleValues = {}, args) {
	let compiledStyles = '';

	const { responsiveDevicesAsIdWidth } = useResponsiveDevices();

	Object.keys(responsiveDevicesAsIdWidth.value).forEach(deviceId => {
		const pseudoStyleValue = styleValues[deviceId];
		if (pseudoStyleValue) {
			let pseudoStyles = getPseudoStyles(cssSelector, pseudoStyleValue, args);
			compiledStyles += getResponsiveDeviceStyles(deviceId, pseudoStyles);
		}
	});
	return compiledStyles;
}

export function getPseudoStyles(cssSelector, pseudoSelectors = {}, args) {
	let combinedStyles = '';

	if (args.forcehoverState) {
		if (typeof pseudoSelectors[':hover'] !== 'undefined') {
			const pseudoStyleValues = pseudoSelectors[':hover'];
			combinedStyles += compilePseudoStyle(cssSelector, 'default', pseudoStyleValues);
		}
	} else {
		Object.keys(pseudoSelectors).forEach(pseudoSelectorId => {
			const pseudoStyleValues = pseudoSelectors[pseudoSelectorId];
			combinedStyles += compilePseudoStyle(cssSelector, pseudoSelectorId, pseudoStyleValues);
		});
	}

	return combinedStyles;
}

function compilePseudoStyle(cssSelector, pseudoSelector, styleValues) {
	const append = pseudoSelector !== 'default' ? `${pseudoSelector}` : '';
	const compiledStyles = compileStyleTabs(styleValues);
	const content = styleValues.content;
	const contentStyle = content && content.length > 0 ? `content: '${content}';` : '';

	if (contentStyle || compiledStyles.length > 0) {
		return `${cssSelector}${append} { ${contentStyle}${compiledStyles} }`;
	}

	return '';
}

export function getResponsiveDeviceStyles(deviceId, styles) {
	// Don't proceed if we do not have
	if (!deviceId || !styles) {
		return '';
	}

	const { responsiveDevicesAsIdWidth } = useResponsiveDevices();

	const responsiveWidthValue = responsiveDevicesAsIdWidth.value[deviceId];

	const start = deviceId !== 'default' ? `@media (max-width: ${responsiveWidthValue}px ) {` : '';
	const end = deviceId !== 'default' ? `}` : '';

	return `${start}${styles}${end}`;
}

export function compileStyleTabs(styleValues) {
	let combineStyles = '';
	let filtersGroup = '';
	const backgroundImageConfig = [];

	const {
		// Background Image
		'background-gradient': backgroundGradient,
		'background-image': backgroundImage,
		'background-size': backgroundSize,
		'background-size-units': backgroundSizeUnits = {},
		'background-position-x': backgroundPositionX = '',
		'background-position-y': backgroundPositionY = '',
		// Typography
		'text-decoration': textDecoration,
		'text-shadow': textShadow = {},
		'box-shadow': boxShadow = {},
		// Borders
		border = {},
		'border-radius': borderRadius = {},
		transform: transform = [],
		// Transitions
		'transition-property': transitionProperty = 'all',
		'transition-duration': transitionDuration = 0,
		'transition-timing-function': transitionTimingFunction = 'linear',
		'transition-delay': transitionDelay = 0,
		// Special styles that we don't want to print
		'flex-reverse': flexReverse = false,
		'background-video': backGroundVideo = false,
		'custom-order': customFlexOrder = null,
		order: flexOrder = null,
		transform_origin_x_axis: transformOriginX,
		transform_origin_y_axis: transformOriginY,
		transform_origin_z_axis: transformOriginZ,
		...keyValueStyles
	} = styleValues;

	// Key value styles
	const filterProperties = [
		'grayscale',
		'sepia',
		'blur',
		'brightness',
		'saturate',
		'opacity',
		'contrast',
		'hue-rotate',
		'invert',
	];

	const specialValues = {
		'flex-direction': value => {
			if (!flexReverse) {
				return value === 'row'
					? `-webkit-box-orient: horizontal; -webkit-box-direction:normal;  -ms-flex-direction: ${value}; flex-direction: ${value};`
					: `-webkit-box-orient: vertical; -webkit-box-direction:normal;  -ms-flex-direction: ${value}; flex-direction: ${value};`;
			} else {
				return value === 'row'
					? `-webkit-box-orient: horizontal; -webkit-box-direction:reverse; -ms-flex-direction: row-reverse; flex-direction: row-reverse;`
					: `-webkit-box-orient: vertical; -webkit-box-direction:reverse; -ms-flex-direction: column-reverse; flex-direction: column-reverse;`;
			}
		},
		'custom-order': value => {
			return `-ms-flex-order: ${value}; order: ${value};`;
		},
		order: value => {
			return `-ms-flex-order: ${value}; order: ${value};`;
		},
		'align-items': value => {
			let todelete = /flex-/gi;
			let cleanValue = value.replace(todelete, '');
			return `-webkit-box-align: ${cleanValue}; -ms-flex-align: ${cleanValue}; align-items: ${value};`;
		},
		'justify-content': value => {
			if (value === 'space-around') {
				return `-ms-flex-pack: distribute; justify-content: space-around;`;
			} else if (value === 'space-between') {
				return `-webkit-box-pack: justify; -ms-flex-pack: justify; justify-content: space-between;`;
			} else {
				let todelete = /flex-/gi;
				let cleanValue = value.replace(todelete, '');
				return `-webkit-box-pack: ${cleanValue}; -ms-flex-pack: ${cleanValue}; justify-content: ${value};`;
			}
		},
		'flex-wrap': value => {
			return `-ms-flex-wrap: ${value}; flex-wrap: ${value};`;
		},
		'align-content': value => {
			if (value === 'space-around') {
				return `-ms-flex-line-pack: distribute; align-content: space-around;`;
			} else if (value === 'space-between') {
				return `-ms-flex-line-pack: justify; align-content: space-between;`;
			} else {
				let todelete = /flex-/gi;
				let cleanValue = value.replace(todelete, '');
				return `-ms-flex-line-pack: ${cleanValue}; align-content: ${value};`;
			}
		},
		'flex-grow': value => {
			return `-webkit-box-flex: ${value}; -ms-flex-positive: ${value}; flex-grow: ${value};`;
		},
		'flex-shrink': value => {
			return `-ms-flex-negative: ${value};flex-shrink: ${value};`;
		},
		'flex-basis': value => {
			return `-ms-flex-preferred-size: ${value}; flex-basis: ${value};`;
		},
		'align-self': value => {
			let todelete = /flex-/gi;
			let cleanValue = value.replace(todelete, '');
			return `-ms-flex-item-align: ${cleanValue}; align-self:${value};`;
		},
		perspective: value => {
			return `perspective: ${value};`;
		},
		transform_style: value => {
			return `-ms-transform-style: ${value}; -webkit-transform-style: ${value}; transform-style: ${value};`;
		},
	};

	// Flex order
	if (flexOrder || customFlexOrder) {
		const orderValue = customFlexOrder || flexOrder;
		combineStyles += `-ms-flex-order: ${orderValue}; order: ${orderValue};`;
	}

	/**
	 * Loop trough all properties and print the css data
	 */
	Object.keys(keyValueStyles).forEach(property => {
		const value = keyValueStyles[property];

		if (property !== '__dynamic_content__' && (value || value === 0)) {
			// Add prefixes
			if (filterProperties.includes(property)) {
				if (property === 'hue-rotate') {
					filtersGroup += `${property}(${value}deg) `;
				} else if (property === 'blur') {
					filtersGroup += `${property}(${value}px) `;
				} else filtersGroup += `${property}(${value}%) `;
			} else if (typeof specialValues[property] === 'function') {
				combineStyles += specialValues[property](value);
			} else {
				combineStyles += `${property}: ${value};`;
			}
		}
	});

	/**
	 * Check for transform
	 */
	if (transform.length) {
		let transformStyleString = '';
		let originStyleString = '';
		let perspectiveOrigin = {};

		transform.forEach(transformProperty => {
			const property = transformProperty.property || 'translate';
			const currentPropertyValues = transformProperty[property];
			for (const propertyName in currentPropertyValues) {
				if (property === 'transform-origin') {
					originStyleString += `${currentPropertyValues[propertyName]} `;
				} else if (property === 'perspective') {
					if (propertyName === 'perspective_value') {
						transformStyleString += `perspective(${currentPropertyValues[propertyName]}) `;
					}
					if (propertyName === 'perspective_origin_x_axis') {
						perspectiveOrigin.x = `${currentPropertyValues[propertyName]}`;
					}
					if (propertyName === 'perspective_origin_y_axis') {
						perspectiveOrigin.y = `${currentPropertyValues[propertyName]}`;
					}
				} else {
					transformStyleString += `${propertyName}(${currentPropertyValues[propertyName]}) `;
				}
			}
		});

		if (transformStyleString) {
			combineStyles += `-webkit-transform: ${transformStyleString};-ms-transform: ${transformStyleString};transform: ${transformStyleString};`;
		}

		if (originStyleString) {
			combineStyles += `-webkit-transform-origin: ${originStyleString}; transform-origin: ${originStyleString};`;
		}

		if (perspectiveOrigin.y !== undefined || perspectiveOrigin.x !== undefined) {
			let xAxis = perspectiveOrigin.x !== undefined ? perspectiveOrigin.x : '50%';
			let yAxis = perspectiveOrigin.y !== undefined ? perspectiveOrigin.y : '50%';

			combineStyles += `-ms-perspective-origin: ${xAxis} ${yAxis}; -moz-perspective-origin: ${xAxis} ${yAxis}; -webkit-perspective-origin: ${xAxis} ${yAxis}; perspective-origin: ${xAxis} ${yAxis};`;
		}
	}

	// Check for transform origin
	if (transformOriginX || transformOriginY || transformOriginZ) {
		let originX = transformOriginX || '50%';
		let originY = transformOriginY || '50%';
		let originZ = transformOriginZ || '0';
		combineStyles += `transform-origin: ${originX} ${originY} ${originZ};-webkit-transform-origin: ${originX} ${originY} ${originZ};`;
	}

	// Background gradient
	if (backgroundGradient) {
		const gradientConfig = getGradientCss(backgroundGradient);
		if (gradientConfig) {
			backgroundImageConfig.push(gradientConfig);
		}
	}

	// Background image
	if (backgroundImage) {
		backgroundImageConfig.push(`url(${backgroundImage})`);
	}

	// background position
	if (backgroundPositionX || backgroundPositionY) {
		const xPosition = backgroundPositionX || '50%';
		const yPosition = backgroundPositionY || '50%';
		combineStyles += `background-position: ${xPosition} ${yPosition};`;
	}

	// Background image
	if (backgroundImageConfig.length > 0) {
		combineStyles += `background-image: ${backgroundImageConfig.join(', ')};`;
	}

	// Background size
	if (backgroundSize && backgroundSize !== 'custom') {
		combineStyles += `background-size: ${backgroundSize};`;
	} else if (backgroundSize === 'custom') {
		const { x, y } = backgroundSizeUnits;
		if (x || y) {
			const { x = 'auto', y = 'auto' } = backgroundSizeUnits;
			combineStyles += `background-size: ${x} ${y};`;
		}
	}

	// Text decoration
	if (textDecoration) {
		let textDecorationValue = [];
		if (textDecoration.includes('underline')) {
			textDecorationValue.push('underline');
		}
		if (textDecoration.includes('line-through')) {
			textDecorationValue.push('line-through');
		}
		if (textDecorationValue.length > 0) {
			const textDecorationValueString = textDecorationValue.join(' ');
			combineStyles += `text-decoration: ${textDecorationValueString};`;
		}

		if (textDecoration.includes('italic')) {
			combineStyles += `font-style: italic;`;
		}
	}

	// Text shadow
	const shadow = compileShadow(textShadow);
	if (shadow) {
		combineStyles += `text-shadow: ${shadow};`;
	}

	// Border
	const borderStyles = compileBorder(border);
	if (borderStyles) {
		combineStyles += borderStyles;
	}

	// Border radius
	const borderRadiusStyles = compileBorderRadius(borderRadius);
	if (borderRadiusStyles) {
		combineStyles += borderRadiusStyles;
	}

	// let filtersGroup = ''
	let transformGroup = {};
	// let flexDirection = ''
	// let flexReverse = false
	// let customOrder = false
	// let hasPerspective = false

	// Object.keys(keyValueStyles).forEach(property => {
	// 	const value = keyValueStyles[property]

	// 	if (value || value === 0) {
	// 		// Add prefixes
	// 		if (filterProperties.includes(property)) {
	// 			if (property === 'hue-rotate') {
	// 				filtersGroup += (`${property}(${value}deg) `)
	// 			} else if (property === 'blur') {
	// 				filtersGroup += (`${property}(${value}px) `)
	// 			} else filtersGroup += (`${property}(${value}%) `)
	// 		}

	// 		if (renderSpecialPrefix[property] !== undefined) {
	// 			combineStyles += renderSpecialPrefix[property](value)
	// 		} else {
	// 			debugger
	// 			combineStyles += (flexReverse || filtersGroup.length || transformGroup['x'] !== undefined || transformGroup['y'] !== undefined || transformGroup['z'] !== undefined || customOrder || flexDirection.length || hasPerspective || hasPerspectiveValue) ? '' : `${property}: ${value};`
	// 		}

	// 		if (value === 'flex') {
	// 			combineStyles += `display: -webkit-box; display: -moz-box; display: -ms-flexbox; display: -webkit-flex; `
	// 		}

	// 		if (value === 'inline-flex') {
	// 			combineStyles += `display: -webkit-inline-box; display: -ms-inline-flexbox; `
	// 		}
	// 	}
	// })

	if (filtersGroup.length) {
		combineStyles += `-webkit-filter: ${filtersGroup};filter: ${filtersGroup};`;
	}

	if (transformGroup['x'] !== undefined || transformGroup['y'] !== undefined || transformGroup['z'] !== undefined) {
		let xAxis = transformGroup['x'] !== undefined ? transformGroup['x'] : '50%';
		let yAxis = transformGroup['y'] !== undefined ? transformGroup['y'] : '50%';
		let zAxis = transformGroup['z'] !== undefined ? transformGroup['z'] : '';
		combineStyles += `-webkit-transform-origin: ${xAxis} ${yAxis} ${zAxis}; transform-origin: ${xAxis} ${yAxis} ${zAxis};`;
	}

	// Box shadow
	if (boxShadow) {
		const shadow = compileShadow(boxShadow);
		if (shadow) {
			combineStyles += `box-shadow: ${shadow};`;
		}
	}

	// Transitions
	if (transitionDuration) {
		let delayCompiled = transitionDelay !== 0 ? `${transitionDelay}ms` : '';
		combineStyles += `transition: ${transitionProperty} ${transitionDuration}ms ${transitionTimingFunction} ${delayCompiled};`;
	}

	return combineStyles;
}

export function getGradientCss(config) {
	let gradient = [];
	let position;

	config.forEach(element => {
		let colors = [];

		const colorsCopy = [...element.colors].sort((a, b) => {
			return a.position > b.position ? 1 : -1;
		});

		colorsCopy.forEach(color => {
			colors.push(`${color.color} ${color.position}%`);
		});

		// Set position
		if (element.type === 'radial') {
			const { x, y } = element.position || {
				x: 50,
				y: 50,
			};
			position = `circle at ${x}% ${y}%`;
		} else {
			position = `${element.angle}deg`;
		}

		gradient.push(`${element.type}-gradient(${position}, ${colors.join(', ')})`);
	});
	gradient.reverse();

	return gradient.join(', ');
}

function compileShadow(textShadowValue) {
	let { 'offset-x': offsetX, 'offset-y': offsetY, blur, spread, color, inset } = textShadowValue;

	if (offsetX || offsetY || blur || spread || color || inset) {
		offsetX = offsetX || 0;
		offsetY = offsetY || 0;

		const shadowList = [offsetX, offsetY];
		if (blur) {
			shadowList.push(blur);
		}

		if (spread) {
			shadowList.push(spread);
		}

		if (color) {
			shadowList.push(color);
		}

		if (inset) {
			shadowList.push('inset');
		}

		return shadowList.join(' ');
	}

	return null;
}

export function compileFontTab(styleValues) {
	let css = '';

	const {
		'font-display': fontDisplayGroup = {},
		'font-style': fontStyleGroup = {},
		'font-typography': fontTypography,
		'text-shadow': textShadow,
		...dynamicValues
	} = styleValues;

	const { 'text-decoration': textDecoration, ...remainingFontStyleGroup } = fontStyleGroup;

	const keyValueProperties = {
		...dynamicValues,
		...remainingFontStyleGroup,
	};

	const { 'line-height': lineHeight, 'letter-spacing': letterSpacing } = fontDisplayGroup;

	if (fontTypography) {
		const { 'font-family': fontFamily, 'font-settings': fontSettings } = fontTypography;

		if (fontFamily) {
			css += `font-family: ${fontFamily};`;
		}

		if (fontSettings) {
			const { 'font-size': fontSize, ...remainingProperties } = fontSettings;

			if (fontSize) {
				css += `font-size: ${fontSize};`;
			}

			Object.keys(remainingProperties).forEach(cssProperty => {
				if (remainingProperties[cssProperty]) {
					css += `${cssProperty}: ${remainingProperties[cssProperty]};`;
				}
			});
		}
	}

	if (lineHeight) {
		css += `line-height: ${lineHeight};`;
	}

	if (letterSpacing) {
		css += `letter-spacing: ${letterSpacing};`;
	}

	if (textDecoration) {
		let textDecorationValue = [];
		if (textDecoration.includes('underline')) {
			textDecorationValue.push('underline');
		}
		if (textDecoration.includes('strikethrough')) {
			textDecorationValue.push('line-through');
		}
		if (textDecorationValue.length > 0) {
			const textDecorationValueString = textDecorationValue.join(' ');
			css += `text-decoration: ${textDecorationValueString};`;
		}

		if (textDecoration.includes('italic')) {
			css += `font-style: italic;`;
		}
	}

	if (textShadow) {
		const shadow = compileShadow(textShadow);
		if (shadow) {
			css += `text-shadow: ${shadow};`;
		}
	}

	Object.keys(keyValueProperties).forEach(cssProperty => {
		if (keyValueProperties[cssProperty]) {
			css += `${cssProperty}: ${keyValueProperties[cssProperty]};`;
		}
	});

	return css;
}

function compileBorder(borderValue) {
	let css = '';
	Object.keys(borderValue).forEach(borderPosition => {
		const allBorders = borderPosition === 'all';

		if (!borderValue[borderPosition]) {
			return;
		}

		const { width, color, style } = borderValue[borderPosition];

		if (!width) {
			return;
		}

		if (typeof width !== 'undefined') {
			const styleValue = style || 'solid';
			const colorValue = color || '';
			if (!allBorders) {
				css += `border-${borderPosition}: ${width} ${styleValue} ${colorValue};`;
			} else {
				css += `border: ${width} ${styleValue} ${colorValue};`;
			}
		}
	});
	return css;
}

function compileBorderRadius(borderRadiusValue) {
	let css = '';

	if (borderRadiusValue && Object.keys(borderRadiusValue).length === 0) {
		return css;
	}

	const borderTopLeft =
		typeof borderRadiusValue['border-top-left-radius'] !== 'undefined'
			? borderRadiusValue['border-top-left-radius']
			: 0;
	const borderTopRight =
		typeof borderRadiusValue['border-top-right-radius'] !== 'undefined'
			? borderRadiusValue['border-top-right-radius']
			: 0;
	const borderBottomLeft =
		typeof borderRadiusValue['border-bottom-left-radius'] !== 'undefined'
			? borderRadiusValue['border-bottom-left-radius']
			: 0;
	const borderBottomRight =
		typeof borderRadiusValue['border-bottom-right-radius'] !== 'undefined'
			? borderRadiusValue['border-bottom-right-radius']
			: 0;

	const bordersArray = [borderTopLeft, borderTopRight, borderBottomLeft, borderBottomRight];
	const equalBorders = bordersArray.every(v => v === bordersArray[0]);

	if (equalBorders) {
		css += `border-radius: ${borderTopLeft};`;
	} else {
		css += `border-radius: ${borderTopLeft} ${borderTopRight} ${borderBottomRight} ${borderBottomLeft};`;
	}

	return css;
}
