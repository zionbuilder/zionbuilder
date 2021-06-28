<template>
	<div
		class="znpb-form-colorpicker__color-picker-holder"
		:class="{['color-picker-holder--has-library']: showLibrary}"
		:style="pickerStyle"
	>
		<ColorBoard
			:modelValue="color"
			@update:modelValue="updateColor"
		/>

		<div class="znpb-form-colorpicker-inner__panel">
			<PanelHex
				:modelValue="color"
				@update:modelValue="updateColor"
			/>

			<slot name="end" />
		</div>
	</div>
</template>

<script>

/**
* properties received:
	model
	showLibrary
 *  might be appended to body
 */

import tinycolor from 'tinycolor2'
import PanelHex from './PanelHex.vue'
import ColorBoard from './ColorBoard.vue'

export default {
	name: 'ColorPicker',
	props: {
		model: {
			type: [String, Object],
			required: false
		},
		showLibrary: {
			type: Boolean,
			required: false,
			default: true
		},
		appendTo: {
			type: String,
			required: false
		},
		zIndex: {
			type: Number,
			required: false
		}
	},
	components: {
		ColorBoard,
		PanelHex
	},
	data () {
		return {
			oldHue: 0,
			color: null,
			format: null,
			localLibrary: this.showLibrary
		}
	},
	watch: {
		model (newVal) {
			this.color = this.getColorObject(newVal)
		}
	},
	computed: {
		pickerStyle () {
			if (this.appendTo) {
				return {
					zIndex: this.zIndex
				}
			}

			return null
		},
		appendToElement () {
			return document.querySelector(this.appendTo)
		}
	},
	mounted () {
		// Check if we need to move picker to body
		if (this.appendTo) {
			this.appendPicker()
		}
	},
	methods: {

		updateColor (newValue) {
			let colorObject
			this.oldHue = newValue.h || this.oldHue

			colorObject = tinycolor(newValue)
			const alpha = colorObject.getAlpha()

			let format = newValue.format || this.format

			if (alpha === 1 && format === 'hex8') {
				format = 'hex'
			} else if (alpha < 1 && format === 'hex') {
				format = 'hex8'
			}

			let newColor
			if (format === 'hsl') {
				newColor = colorObject.toHslString()
			} else if (format === 'rgb' || format === 'name' || format === 'hsv') {
				newColor = colorObject.toRgbString()
			} else if (format === 'hex') {
				// if already string send the received value
				newColor = typeof (newValue) === 'string' ? newValue : colorObject.toHexString()
			} else if (format === 'hex8') {
				// if already string send the string
				newColor = typeof (newValue) === 'string' ? newValue : colorObject.toHex8String()
			}

			this.format = format
			this.color = this.getColorObject(newValue)

			if (newValue) {
				this.$emit('color-changed', newColor)
			} else {
				this.$emit('color-changed', '')
			}
		},
		getColorObject (model) {
			const colorObject = tinycolor(model)

			let hsva = colorObject.toHsv()
			let hsla = colorObject.toHsl()
			// if already string send the model
			let hex = typeof (model) === 'string' ? model : colorObject.toHexString()
			let hex8 = typeof (model) === 'string' ? model : colorObject.toHex8String()
			let rgba = colorObject.toRgb()

			// If we do not have a model, set empty values
			if (!model) {
				hex = ''
				hex8 = ''
			}

			if (hsla.s === 0) {
				hsva.h = hsla.h = this.oldHue
			}

			let format = colorObject.format || colorObject.getFormat()

			if ((format === false) || (format === 'name') || (format === 'hsv')) {
				if (colorObject.getAlpha() < 1) {
					format = 'hex8'
				} else {
					format = 'hex'
				}
			}

			if (!this.format) {
				this.format = format
			} else {
				format = this.format
			}

			if (colorObject.type === 'hsva') {
				hsva = model
			}

			return {
				source: model,
				hex,
				hex8,
				rgba,
				hsla,
				hsva,
				format,
				colorObject
			}
		},
		appendPicker () {
			if (!this.appendToElement) {
				// eslint-disable-next-line
				console.warn(`${this.$translate('no_html_matching')} ${this.appendTo}`)
				return
			}
			// append the color Picker to node
			this.appendToElement.appendChild(this.$el)
		},
		getColorFormat (color) {
			let modelnew = tinycolor(color)
			let format = modelnew.getFormat()
			if ((format === false) || (format === 'name') || (format === 'hsv')) {
				return 'hex'
			} else return format
		},
		addGlobal (name) {
			let globalColor = {
				id: name.split(' ').join('_'),
				color: this.model,
				name: name
			}
			this.localLibrary = true
			this.addGlobalColor(globalColor)
		}
	},
	created () {
		this.color = this.getColorObject(this.model)
		this.format = this.color.format
	}
}
</script>

<style lang="scss">
.znpb-form-colorpicker__color-picker-holder {
	overflow: hidden;
	min-width: 280px;
	max-height: 382px;
	border-radius: 3px;
	user-select: none;
}
.znpb-form-colorpicker-inner__panel {
	position: relative;
	display: flex;
	flex-direction: column;
	overflow: hidden;
	max-width: 280px;
	border-bottom-right-radius: 3px;

	// background-color: var(--zb-surface-lighter-color);
	border-bottom-left-radius: 3px;

	.znpb-preset-input-wrapper {
		background: #fff;
		.znpb-editor-icon-wrapper {
			cursor: pointer;
		}
	}
}
.znpb-colorpicker {
	&-circle {
		position: relative;
		flex-shrink: 0;
		width: 14px;
		height: 14px;
		border-radius: 2px;
		cursor: pointer;
		&-color {
			display: block;
		}
		&--opacity {
			@extend %opacitybg;
		}
	}
	&-add-color {
		display: flex;
		justify-content: center;
		align-items: center;
		border: 2px dashed var(--zb-surface-border-color);
	}
}
.znpb-input-number-arrow-wrapper {
	display: flex;
	flex-direction: column;
	color: var(--zb-surface-icon-color);
	background-color: var(--zb-surface-lighter-color);
	box-shadow: -3px 0 3px 0 rgba(0, 0, 0, 0.1);
	border-radius: 3px;
	border-bottom-left-radius: 0;
	border-top-left-radius: 0;
	cursor: pointer;
}
</style>
