<template>
	<Tooltip
		tooltip-class="hg-popper--no-padding"
		trigger="click"
		placement="right-center"
		append-to="body"
		ref="popper"
		:modifiers="{
				flip: {
					behavior: ['left', 'bottom', 'top']
				},
				offset: { offset: '0,10px' },
				preventOverflow: {
					boundariesElement: 'viewport',
				},
			}"
		@show="openColorPicker"
		@hide="closeColorPicker"
		:positionFixed="true"
	>
		<template v-slot:content>
			<ColorPicker
				ref="colorpickerHolder"
				:model="modelValue"
				@color-changed="updateColor"
				@click.stop="onColorPickerClick"
				@mousedown.stop="onColorPickerMousedown"
			>
				<template v-slot:end>
					<PatternContainer
						v-if="showLibrary"
						@color-updated="onLibraryUpdate"
						:model="modelValue"
						:active-tab="dynamicContentConfig ? 'global' : 'local'"
					/>
				</template>
			</ColorPicker>
		</template>


		<slot name="trigger" />

	</Tooltip>
</template>
<script>
import PatternContainer from './PatternContainer.vue'
import { Tooltip } from '@zionbuilder/tooltip'
import { ColorPicker } from '../Colorpicker'

export default {
	name: 'Color',

	components: {
		ColorPicker,
		Tooltip,
		PatternContainer
	},
	props: {
		/**
		* color picker modelValue
		*/
		modelValue: {
			type: String,
			required: false
		},
		/**
		* If the color picker should be appended
		*/
		appendTo: {
			type: String,
			required: false
		},
		/**
		* If the color picker should show library with global and local values
		*/
		showLibrary: {
			type: Boolean,
			required: false,
			default: true
		},
		dynamicContentConfig: {
			type: Object,
			required: false
		}
	},
	computed: {

	},
	methods: {

		onLibraryUpdate (newValue) {
			this.$emit('update:modelValue', newValue)
		},
		onColorPickerClick () {
			this.isDragging = false
		},
		onColorPickerMousedown () {
			this.isDragging = true
		},
		updateColor (color) {
			/**
			* emits new color when color changed
			*/
			this.$emit('option-updated', color)
			/**
			* emits new color when inputcolor changed
			*/
			this.$emit('update:modelValue', color)
		},
		openColorPicker () {
			this.$emit('open')
			document.addEventListener('click', this.closePanelOnOutsideClick, true)
			document.body.classList.add('znpb-color-picker--backdrop')
		},
		closeColorPicker () {
			this.$emit('close')
			document.removeEventListener('click', this.closePanelOnOutsideClick)
			document.body.classList.remove('znpb-color-picker--backdrop')
		},
		closePanelOnOutsideClick (event) {
			if (this.$el.contains(event.target) || (this.$refs.colorpickerHolder && this.$refs.colorpickerHolder.$el.contains(event.target))) {
			} else {
				if (!this.isDragging && this.$refs.popper) {
					this.$refs.popper.hidePopper()
				}
				this.isDragging = false
			}
		}

	},
	beforeUnmount () {
		document.body.classList.remove('znpb-color-picker--backdrop')
		document.removeEventListener('click', this.closePanelOnOutsideClick)
	}
}
</script>

<style lang="scss">
.znpb-color-picker--backdrop, .znpb-color-gradient--backdrop {
	&:before {
		content: "";
		position: absolute;
		width: 100%;
		height: 100%;
	}
}
</style>
