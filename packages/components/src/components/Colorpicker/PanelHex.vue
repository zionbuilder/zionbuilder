<template>
	<div class="znpb-colorpicker-inner-editor">
		<div class="znpb-colorpicker-inner-editor__colors">
			<div class="znpb-colorpicker-inner-editor__current-color">
				<span class="znpb-colorpicker-circle znpb-colorpicker-circle--opacity">
					<span
						:style="{backgroundColor: modelValue.hex8}"
						class="znpb-colorpicker-circle znpb-colorpicker-circle-color"
					/>
				</span>
			</div>
			<div class="znpb-colorpicker-inner-editor__stripes">
				<HueStrip v-model="hslaValue" />
				<OpacityStrip v-model="hslaValue" />
			</div>
		</div>
		<div class="znpb-colorpicker-inner-editor__rgba">
			<RgbaElement
				v-if="modelValue.format === 'rgb'"
				v-model="rgbaValue"
			/>

			<HslaElement
				v-if="modelValue.format === 'hsl'"
				v-model="hslaValue"
			/>
			<HexElement
				v-if="modelValue.format === 'hex' || modelValue.format === 'hex8' || modelValue.format === 'name'"
				v-model="hexValue"
			/>
			<div class="znpb-color-picker-change-color znpb-input-number-arrow-wrapper">
				<Icon
					icon="select"
					:rotate="180"
					class="znpb-arrow-increment"
					@click="changeHex"
				></Icon>
				<Icon
					icon="select"
					class="znpb-arrow-decrement"
					@click="changeHexback"
				></Icon>
			</div>
		</div>
	</div>
</template>

<script>
import { Icon } from '../Icon'
/*
* this element emits change-opacity, change of hue, change of rgba, change, of hex, change of hsla and change of format
*/
import HueStrip from './HueStrip.vue'
import OpacityStrip from './OpacityStrip.vue'
import RgbaElement from './RgbaElement.vue'
import HslaElement from './HslaElement.vue'
import HexElement from './HexElement.vue'

export default {
	name: 'PanelHex',
	components: {
		RgbaElement,
		HslaElement,
		HexElement,
		HueStrip,
		OpacityStrip,
		Icon
	},
	props: {
		modelValue: {}
	},
	data () {
		return {}
	},
	computed: {
		hexValue: {
			get () {
				return this.modelValue.format === 'hex8' ? this.modelValue.hex8 : this.modelValue.hex
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},
		hslaValue: {
			get () {
				return this.modelValue.hsla
			},
			set (hsla) {
				this.$emit('update:modelValue', hsla)
			}
		},
		rgbaValue: {
			get () {
				return this.modelValue.rgba
			},
			set (rgba) {
				this.$emit('update:modelValue', rgba)
			}
		}
	},
	methods: {
		changeHex () {
			let format
			if (this.modelValue.format === 'hex' || this.modelValue.format === 'hex8' || this.modelValue.format === 'name') {
				format = 'hsl'
			} else if (this.modelValue.format === 'hsl') {
				format = 'rgb'
			} else if (this.modelValue.format === 'rgb') {
				format = 'hex'
			}
			this.$emit('update:modelValue', {
				...this.modelValue.hsla,
				format
			})
		},
		changeHexback () {
			let format
			if (this.modelValue.format === 'hsl') {
				format = 'hex'
			} else if (this.modelValue.format === 'rgb') {
				format = 'hsl'
			} else if (this.modelValue.format === 'hex' || this.modelValue.format === 'hex8' || this.modelValue.format === 'name') {
				format = 'rgb'
			}
			this.$emit('update:modelValue', {
				...this.modelValue.hsla,
				format
			})
		}
	}
}
</script>
<style lang="scss">
.znpb-colorpicker-inner-editor {
	display: flex;
	flex-direction: column;
	flex-shrink: 0;
	padding: 20px 20px 12px;
	background-color: var(--zb-surface-color);

	&__colors {
		display: flex;
		margin-bottom: 15px;
	}

	&__current-color {
		width: 30px;
		height: 30px;

		.znpb-colorpicker-circle {
			display: block;
			width: 100%;
			height: 100%;
		}
	}

	&__stripes {
		flex: 1;
		margin-left: 10px;
	}

	&__hue,
	&__opacity {
		position: relative;
		width: 100%;
		height: 8px;
	}
	.znpb-colorpicker-circle--opacity {
		position: relative;

		.znpb-colorpicker-circle {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1);
		}
	}

	&__rgba {
		display: flex;
		justify-content: space-between;

		.zion-icon.zion-picker {
			align-items: center;
			height: 30px;
			padding: 0 3px;
			font-size: 10px;
		}

		.znpb-color-picker-change-color {
			margin: 3px 0 0 10px;
			background: none;
			box-shadow: none;

			.znpb-arrow-increment,
			.znpb-arrow-decrement {
				width: 12px;
				margin: 0;
				color: var(--zb-surface-icon-color);
				background: none;
				border-radius: 0;
				transition: color 0.1s;

				&:hover {
					color: var(--zb-surface-text-hover-color);
				}
			}
		}
	}
}
</style>
