const responsiveDevices = {
	laptop: '991.98px',
	tablet: '767.98px',
	mobile: '575.98px'
}

export function getStyles (cssSelector, styleValues = {}) {
	let compiledStyles = ''
	const devices = [
		'default',
		'laptop',
		'tablet',
		'mobile'
	]

	devices.forEach((deviceId) => {
		const pseudoStyleValue = styleValues[deviceId]
		if (pseudoStyleValue) {
			let pseudoStyles = getPseudoStyles(cssSelector, pseudoStyleValue)
			compiledStyles += getResponsiveDeviceStyles(deviceId, pseudoStyles)
		}
	})
	return compiledStyles
}

export function getPseudoStyles (cssSelector, pseudoSelectors = {}) {
	let combinedStyles = ''

	Object.keys(pseudoSelectors).forEach((pseudoSelectorId) => {
		const pseudoStyleValues = pseudoSelectors[pseudoSelectorId]
		combinedStyles += compilePseudoStyle(cssSelector, pseudoSelectorId, pseudoStyleValues)
	})

	return combinedStyles
}

function compilePseudoStyle (cssSelector, pseudoSelector, styleValues) {
	const append = pseudoSelector !== 'default' ? `${pseudoSelector}` : ''
	const compiledStyles = compileStyleTabs(styleValues)
	const content = styleValues.content
	const contentStyle = content && content.length > 0 ? `content: '${content}';` : ''

	if (contentStyle || compiledStyles.length > 0) {
		return `${cssSelector}${append} { ${contentStyle}${compiledStyles} }`
	}

	return ''
}

export function getResponsiveDeviceStyles (deviceId, styles) {
	// Don't proceed if we do not have
	if (!deviceId || !styles) {
		return ''
	}

	const responsiveWidthValue = responsiveDevices[deviceId]
	const start = deviceId !== 'default' ? `@media (max-width: ${responsiveWidthValue} ) {` : ''
	const end = deviceId !== 'default' ? `}` : ''

	return `${start}${styles}${end}`
}

export function compileStyleTabs (styleValues) {
	let combineStyles = ''

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
		'transform': transform = [],
		// Transitions
		'transition-property': transitionProperty = 'all',
		'transition-duration': transitionDuration = 0,
		'transition-timing-function': transitionTimingFunction = 'linear',
		'transition-delay': transitionDelay = 0,
		...keyValueStyles
	} = styleValues

	const backgroundImageConfig = []

	if (transform.length) {
		let transformStyleString = ''
		let perspectiveStyleString = ''
		let originStyleString = ''
		transform.forEach(transformProperty => {
			const property = transformProperty.property || 'translate'
			const currentPropertyValues = transformProperty[property]
			for (const propertyName in currentPropertyValues) {
				if (property === 'transform-origin') {
					originStyleString += `${currentPropertyValues[propertyName]} `
				} else if (property !== 'perspective') {
					transformStyleString += `${propertyName}(${currentPropertyValues[propertyName]}) `
				} else {
					perspectiveStyleString = ''
					perspectiveStyleString += `${currentPropertyValues[propertyName]} `
				}
			}
		})
		if (transformStyleString) {
			combineStyles += `transform: ${transformStyleString};`
		}
		if (perspectiveStyleString) {
			combineStyles += `perspective: ${perspectiveStyleString};`
		}
		if (originStyleString) {
			combineStyles += `transform-origin: ${originStyleString};`
		}
	}

	// Background gradient
	if (backgroundGradient) {
		const gradientConfig = getGradientCss(backgroundGradient)
		if (gradientConfig) {
			backgroundImageConfig.push(gradientConfig)
		}
	}

	// Background image
	if (backgroundImage) {
		backgroundImageConfig.push(`url(${backgroundImage})`)
	}

	// background position
	if (backgroundPositionX || backgroundPositionY) {
		const xPosition = backgroundPositionX || '50%'
		combineStyles += `background-position: ${xPosition} ${backgroundPositionY};`
	}

	// Background image
	if (backgroundImageConfig.length > 0) {
		combineStyles += `background-image: ${backgroundImageConfig.join(', ')};`
	}

	// Background size
	if (backgroundSize && backgroundSize !== 'custom') {
		combineStyles += `background-size: ${backgroundSize};`
	} else if (backgroundSize === 'custom') {
		const { x, y } = backgroundSizeUnits
		if (x || y) {
			const { x = 'auto', y = 'auto' } = backgroundSizeUnits
			combineStyles += `background-size: ${x} ${y};`
		}
	}

	// Text decoration
	if (textDecoration) {
		let textDecorationValue = []
		if (textDecoration.includes('underline')) {
			textDecorationValue.push('underline')
		}
		if (textDecoration.includes('line-through')) {
			textDecorationValue.push('line-through')
		}
		if (textDecorationValue.length > 0) {
			const textDecorationValueString = textDecorationValue.join(' ')
			combineStyles += `text-decoration: ${textDecorationValueString};`
		}

		if (textDecoration.includes('italic')) {
			combineStyles += `font-style: italic;`
		}
	}

	// Text shadow
	const shadow = compileShadow(textShadow)
	if (shadow) {
		combineStyles += `text-shadow: ${shadow};`
	}

	// Border
	const borderStyles = compileBorder(border)
	if (borderStyles) {
		combineStyles += borderStyles
	}

	// Border radius
	const borderRadiusStyles = compileBorderRadius(borderRadius)
	if (borderRadiusStyles) {
		combineStyles += borderRadiusStyles
	}

	// Key value styles
	Object.keys(keyValueStyles).forEach(property => {
		const value = keyValueStyles[property]
		if (value || value === 0) {
			combineStyles += `${property}: ${value};`
		}
	})

	// Box shadow
	if (boxShadow) {
		const shadow = compileShadow(boxShadow)
		if (shadow) {
			combineStyles += `box-shadow: ${shadow};`
		}
	}

	// Transitions
	if (transitionDuration) {
		combineStyles += `transition: ${transitionProperty} ${transitionDuration}ms ${transitionTimingFunction} ${transitionDelay}ms;`
	}

	return combineStyles
}

export function getGradientCss (config) {
	let gradient = []
	let position

	config.forEach(element => {
		let colors = []

		const colorsCopy = [...element.colors].sort((a, b) => {
			return a.position > b.position ? 1 : -1
		})

		colorsCopy.forEach((color) => {
			colors.push(`${color.color} ${color.position}%`)
		})

		// Set position
		if (element.type === 'radial') {
			const { x, y } = element.position || { x: 50, y: 50 }
			position = `circle at ${x}% ${y}%`
		} else {
			position = `${element.angle}deg`
		}

		gradient.push(`${element.type}-gradient(${position}, ${colors.join(', ')})`)
	})
	gradient.reverse()

	return gradient.join(', ')
}

function compileShadow (textShadowValue) {
	let {
		'offset-x': offsetX,
		'offset-y': offsetY,
		blur,
		spread,
		color,
		inset
	} = textShadowValue

	if (offsetX || offsetY || blur || spread || color || inset) {
		offsetX = offsetX || 0
		offsetY = offsetY || 0

		const shadowList = [offsetX, offsetY]
		if (blur) {
			shadowList.push(blur)
		}

		if (spread) {
			shadowList.push(spread)
		}

		if (color) {
			shadowList.push(color)
		}

		if (inset) {
			shadowList.push('inset')
		}

		return shadowList.join(' ')
	}

	return null
}

export function compileFontTab (styleValues) {
	let css = ''

	const {
		'font-display': fontDisplayGroup = {},
		'font-style': fontStyleGroup = {},
		'font-typography': fontTypography,
		'text-shadow': textShadow,
		...dynamicValues
	} = styleValues

	const {
		'text-decoration': textDecoration,
		...remainingFontStyleGroup
	} = fontStyleGroup

	const keyValueProperties = {
		...dynamicValues,
		...remainingFontStyleGroup
	}

	const {
		'line-height': lineHeight,
		'letter-spacing': letterSpacing
	} = fontDisplayGroup

	if (fontTypography) {
		const { 'font-family': fontFamily, 'font-settings': fontSettings } = fontTypography

		if (fontFamily) {
			css += `font-family: ${fontFamily};`
		}

		if (fontSettings) {
			const { 'font-size': fontSize, ...remainingProperties } = fontSettings

			if (fontSize) {
				css += `font-size: ${fontSize};`
			}

			Object.keys(remainingProperties).forEach((cssProperty) => {
				if (remainingProperties[cssProperty]) {
					css += `${cssProperty}: ${remainingProperties[cssProperty]};`
				}
			})
		}
	}

	if (lineHeight) {
		css += `line-height: ${lineHeight};`
	}

	if (letterSpacing) {
		css += `letter-spacing: ${letterSpacing};`
	}

	if (textDecoration) {
		let textDecorationValue = []
		if (textDecoration.includes('underline')) {
			textDecorationValue.push('underline')
		}
		if (textDecoration.includes('strikethrough')) {
			textDecorationValue.push('line-through')
		}
		if (textDecorationValue.length > 0) {
			const textDecorationValueString = textDecorationValue.join(' ')
			css += `text-decoration: ${textDecorationValueString};`
		}

		if (textDecoration.includes('italic')) {
			css += `font-style: italic;`
		}
	}

	if (textShadow) {
		const shadow = compileShadow(textShadow)
		if (shadow) {
			css += `text-shadow: ${shadow};`
		}
	}

	Object.keys(keyValueProperties).forEach((cssProperty) => {
		if (keyValueProperties[cssProperty]) {
			css += `${cssProperty}: ${keyValueProperties[cssProperty]};`
		}
	})

	return css
}

function compileBorder (borderValue) {
	let css = ''
	Object.keys(borderValue).forEach(borderPosition => {
		const allBorders = borderPosition === 'all'
		const { width, color, style } = borderValue[borderPosition]

		if (!width) {
			return
		}

		if (typeof width !== 'undefined') {
			const styleValue = style || 'solid'
			const colorValue = color || ''
			if (!allBorders) {
				css += `border-${borderPosition}: ${width} ${styleValue} ${colorValue};`
			} else {
				css += `border: ${width} ${styleValue} ${colorValue};`
			}
		}
	})
	return css
}

function compileBorderRadius (borderRadiusValue) {
	let css = ''

	if (borderRadiusValue && Object.keys(borderRadiusValue).length === 0) {
		return css
	}

	const borderTopLeft = typeof borderRadiusValue['border-top-left-radius'] !== 'undefined' ? borderRadiusValue['border-top-left-radius'] : 0
	const borderTopRight = typeof borderRadiusValue['border-top-right-radius'] !== 'undefined' ? borderRadiusValue['border-top-right-radius'] : 0
	const borderBottomLeft = typeof borderRadiusValue['border-bottom-left-radius'] !== 'undefined' ? borderRadiusValue['border-bottom-left-radius'] : 0
	const borderBottomRight = typeof borderRadiusValue['border-bottom-right-radius'] !== 'undefined' ? borderRadiusValue['border-bottom-right-radius'] : 0

	const bordersArray = [borderTopLeft, borderTopRight, borderBottomLeft, borderBottomRight]
	const equalBorders = bordersArray.every(v => v === bordersArray[0])

	if (equalBorders) {
		css += `border-radius: ${borderTopLeft};`
	} else {
		css += `border-radius: ${borderTopLeft} ${borderTopRight} ${borderBottomRight} ${borderBottomLeft};`
	}

	return css
}
