<template>
	<div class="znpb-form-colorpicker">
		<Color
			v-model="colorModel"
			v-if="type === 'simple'"
			@open="$emit('open')"
			@close="$emit('close')"
			:show-library="showLibrary"
			class="znpb-colorpicker-circle  znpb-colorpicker-circle--trigger znpb-colorpicker-circle--opacity"
		>
			<template v-slot:trigger>
				<span
					:style="{backgroundColor: modelValue}"
					class="znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
				></span>

				<Icon
					v-if="dynamicContentConfig"
					icon="globe"
					:rounded="true"
					bg-color="#fff"
					:bg-size="16"
					:size="12"
					class="znpb-colorpicker-circle__global-icon"
				/>

				<Tooltip
					v-if="!modelValue"
					:content="$translate('no_color_chosen')"
				>
					<span class="znpb-form-colorpicker__color-trigger znpb-colorpicker-circle znpb-colorpicker-circle--no-color"></span>
				</Tooltip>

			</template>
		</Color>

		<BaseInput
			v-model="colorModel"
			:placeholder="$translate('color')"
			v-else
		>
			<template v-slot:prepend>
				<Color
					v-model="colorModel"
					@open="$emit('open')"
					@close="$emit('close')"
					:show-library="showLibrary"
					class="znpb-colorpicker-circle  znpb-colorpicker-circle--trigger znpb-colorpicker-circle--opacity"
				>
					<template v-slot:trigger>
						<span>
							<Tooltip
								v-if="!modelValue || modelValue===undefined"
								:content="$translate('no_color_chosen')"
								tag="span"
							>
								<span
									:style="{backgroundColor: modelValue}"
									class="znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
								></span>
							</Tooltip>
							<span v-else
								:style="{backgroundColor: modelValue}"
								class="znpb-form-colorpicker__color-trigger znpb-colorpicker-circle"
							></span>

							<Icon
								v-if="dynamicContentConfig"
								icon="globe"
								:rounded="true"
								bg-color="#fff"
								:bg-size="16"
								:size="12"
								class="znpb-colorpicker-circle__global-icon"
							/>
						</span>
					</template>
				</Color>
			</template>

		</BaseInput>
	</div>
</template>
<script>

/**
* required properties received:
 *   model - string / rgb color / hsla color hsv color hex color
 * other properties received:
 * 	colorscheme - String
 * On change it emits this.$emit('colorpicker-updated', this.localModel)
 */
import tinycolor from 'tinycolor2'
import BaseInput from '../BaseInput/BaseInput.vue'
import Color from './Color.vue'
import { Tooltip } from '@zionbuilder/tooltip'
import { Icon } from '../Icon'

export default {
	name: 'InputColorPicker',
	inheritAttrs: true,
	props: {
		/**
		* color picker modelValue
		*/
		modelValue: {
			type: String,
			required: false
		},
		type: null,
		dynamicContentConfig: {
			type: Object,
			required: false
		},
		showLibrary: {
			type: Boolean,
			default: true
		}
	},
	data () {
		return {
			isDragging: false
		}
	},
	computed: {
		colorModel: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		}
	},
	components: {
		BaseInput,
		Color,
		Tooltip,
		Icon
	},
	methods: {},
	beforeUnmount () {
		document.removeEventListener('click', this.closePanelOnOutsideClick)
	}
}

</script>
<style lang="scss">
.znpb-form-colorpicker {
	position: relative;
	display: flex;
	.zion-input__prepend {
		padding: 0 0 0 10px;
	}
	&__color-trigger {
		position: absolute;
		top: 0;
		left: 0;
		cursor: pointer;
	}
	.znpb-colorpicker-circle {
		box-shadow: 0 0 0 2px #e5e5e5;
		&--no-color {
			background: #fff;
		}
		&__global-icon {
			position: absolute;
			top: -2px;
			right: 0;
			color: $surface-headings-color;
		}
	}
}
span.znpb-form-colorpicker__color-trigger:not(.znpb-colorpicker-circle--no-color) {
	display: block;
}

.znpb-input-wrapper--inline.znpb-input-type--colorpicker > .znpb-input-content {
	display: flex;
	justify-content: flex-end;
}
</style>
