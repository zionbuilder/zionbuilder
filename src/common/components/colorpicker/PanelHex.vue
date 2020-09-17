<template>
	<div class="znpb-colorpicker-inner-editor">
		<div class="znpb-colorpicker-inner-editor__colors">
			<div class="znpb-colorpicker-inner-editor__current-color">
				<span class="znpb-colorpicker-circle znpb-colorpicker-circle--opacity">
					<span
						:style="{backgroundColor: value.hex8}"
						class="znpb-colorpicker-circle znpb-colorpicker-circle-color"
					/>
				</span>
			</div>
			<div class="znpb-colorpicker-inner-editor__stripes">
				<HueStrip
					v-model="hslaValue"
				/>
				<OpacityStrip
					v-model="hslaValue"
				/>
			</div>
		</div>
		<div class="znpb-colorpicker-inner-editor__rgba">
			<RgbaElement
				v-if="value.format === 'rgb'"
				v-model="rgbaValue"
			/>

			<HslaElement
				v-if="value.format === 'hsl'"
				v-model="hslaValue"
			/>
			<HexElement
				v-if="value.format === 'hex' || value.format === 'hex8' || value.format === 'name'"
				v-model="hexValue"
			/>
			<div class="znpb-color-picker-change-color znpb-input-number-arrow-wrapper">
				<BaseIcon icon="select" :rotate="180" class="znpb-arrow-increment" @click.native="changeHex"></BaseIcon>
				<BaseIcon icon="select" class="znpb-arrow-decrement" @click.native="changeHexback"></BaseIcon>
			</div>
		</div>
	</div>
</template>

<script>
import BaseIcon from '@/common/components/BaseIcon.vue'
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
		BaseIcon
	},
	props: {
		value: {}
	},
	data () {
		return {}
	},
	computed: {
		hexValue: {
			get () {
				return this.value.format === 'hex8' ? this.value.hex8 : this.value.hex
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		},
		hslaValue: {
			get () {
				return this.value.hsla
			},
			set (hsla) {
				this.$emit('input', hsla)
			}
		},
		rgbaValue: {
			get () {
				return this.value.rgba
			},
			set (rgba) {
				this.$emit('input', rgba)
			}
		}
	},
	methods: {
		changeHex () {
			let format
			if (this.value.format === 'hex' || this.value.format === 'hex8' || this.value.format === 'name') {
				format = 'hsl'
			} else if (this.value.format === 'hsl') {
				format = 'rgb'
			} else if (this.value.format === 'rgb') {
				format = 'hex'
			}
			this.$emit('input', {
				...this.value.hsla,
				format
			})
		},
		changeHexback () {
			let format
			if (this.value.format === 'hsl') {
				format = 'hex'
			} else if (this.value.format === 'rgb') {
				format = 'hsl'
			} else if (this.value.format === 'hex' || this.value.format === 'hex8' || this.value.format === 'name') {
				format = 'rgb'
			}
			this.$emit('input', {
				...this.value.hsla,
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
	background-color: lighten($surface-variant, 5%);

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

	&__hue, &__opacity {
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
			box-shadow: inset 0 0 0 2px rgba(0, 0, 0, .1);
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

			.znpb-arrow-increment, .znpb-arrow-decrement {
				width: 12px;
				margin: 0;
				color: $surface-headings-color;
				background: none;
				border-radius: 0;
				transition: color .1s;

				&:hover {
					color: darken($surface-headings-color, 15%);
				}
			}
		}
	}
}
</style>
