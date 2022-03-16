<template>
	<Tooltip
		tooltip-class="hg-popper--no-padding"
		trigger="click"
		ref="popper"
		:close-on-outside-click="true"
		append-to="body"
		:modifiers="[
			{
				name: 'preventOverflow',
				options: {
					rootBoundary: 'viewport',
				},
			},
			{
				name: 'offset',
				options: {
					offset: [0, 15],
				},
			},
			{
				name: 'flip',
				options: {
					fallbackPlacements: ['left', 'right','bottom', 'top'],
				},
			},

		]"
		@show="openColorPicker"
		@hide="closeColorPicker"
		strategy="fixed"
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

			if (this.$refs.popper.$el) {
				this.backdrop = document.createElement('div')
				this.backdrop.classList.add('znpb-tooltip-backdrop')
				const popper = this.$refs.popper.$el
				const parent = popper.parentNode
				parent.insertBefore(this.backdrop, popper)
			}
		},
		closeColorPicker () {
			this.$emit('close')
			document.removeEventListener('click', this.closePanelOnOutsideClick)

			if (this.backdrop) {
				document.body.appendChild(this.backdrop)
				this.backdrop.parentNode.removeChild(this.backdrop);
			}

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
		document.removeEventListener('click', this.closePanelOnOutsideClick)
	}
}
</script>

<style lang="scss">
.znpb-tooltip-backdrop {
	width: 100%;
	height: 100%;
	position: fixed;
	top: 0;
	left: 0;
}
</style>