<template>
	<div class="znpb-gradient-bar-colors-wrapper">
		<div
			ref="gradientbar"
			class="znpb-gradient-bar-wrapper"
		>
			<GradientBarPreview
				:config="computedValue"
				@click.native="addColor"
			/>
			<GradientDragger
				v-for="(colorConfig, i) in computedValue.colors"
				:key="i"
				:value="colorConfig"
				@input="onColorConfigUpdate(colorConfig, $event)"
				@color-picker-open="colorPickerOpen=$event"
				@mousedown.native="enableDragging(i, $event)"
			/>
		</div>
		<div class="znpb-gradient-colors-legend">
			<span class="znpb-form__input-title znpb-gradient-colors-legend-item"> {{$translate('color')}} </span>
			<span class="znpb-form__input-title znpb-gradient-colors-legend-item"> {{$translate('location')}} </span>
		</div>
		<GradientColorConfig
			v-for="(colorConfig, i) in sortedColors"
			:key="i"
			:config="colorConfig"
			@input="onColorConfigUpdate(colorConfig, $event)"
			@delete-color="onDeleteColor(colorConfig)"
			:show-delete="sortedColors.length > 2"
		/>
	</div>
</template>
<script>
import GradientBarPreview from './GradientBarPreview.vue'
import GradientDragger from './GradientDragger.vue'
import GradientColorConfig from './GradientColorConfig.vue'
import rafSchd from 'raf-schd'

export default {
	name: 'GradientBar',
	components: {
		GradientBarPreview,
		GradientDragger,
		GradientColorConfig
	},
	props: {
		value: {
			type: Object
		},
		config: {
			type: Object,
			required: false
		}
	},
	data () {
		return {
			draggedCircle: null,
			gradref: {},
			colorPickerOpen: false,
			deletedColorConfig: null

		}
	},
	computed: {
		computedValue: {
			get () {
				return this.value
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		},
		sortedColors () {
			let colorsCopy = [...this.computedValue.colors].sort((a, b) => {
				return a.position > b.position ? 1 : -1
			})

			return colorsCopy
		},
		activeDraggedItem () {
			return this.computedValue.colors[this.draggedCircleIndex]
		}
	},
	mounted () {
		this.$nextTick(() => {
			this.gradref = this.$refs.gradientbar.getBoundingClientRect()
		})
	},
	methods: {
		onColorConfigUpdate (colorConfig, newValues) {
			const index = this.computedValue.colors.indexOf(colorConfig)
			const updatedValues = this.computedValue.colors.slice(0)
			updatedValues.splice(index, 1, newValues)

			this.computedValue = {
				...this.computedValue,
				colors: updatedValues
			}
		},
		onDeleteColor (colorConfig) {
			const index = this.computedValue.colors.indexOf(colorConfig)
			const colorsClone = this.computedValue.colors.slice(0)

			this.deletedColorConfig = colorConfig
			colorsClone.splice(index, 1)

			this.computedValue = {
				...this.computedValue,
				colors: colorsClone
			}
		},
		reAddColor () {
			this.computedValue = {
				...this.computedValue,
				colors: [
					...this.computedValue.colors,
					this.deletedColorConfig
				]
			}

			this.deletedColorConfig = null
		},
		addColor (event) {
			const defaultColor = {
				color: 'white',
				position: 0
			}

			const mouseLeftPosition = event.clientX
			const barOffset = this.$el.getBoundingClientRect()

			// Calculate where the coordinate x of the element starts
			const startx = barOffset.left
			// from total width reduce the coordinate x value
			const newLeft = mouseLeftPosition - startx

			defaultColor.position = parseInt(((newLeft / barOffset.width) * 100), 10)

			const updatedValues = {
				...this.computedValue,
				colors: [
					...this.computedValue.colors,
					defaultColor
				]
			}

			this.computedValue = updatedValues
		},
		enableDragging (colorConfigIndex, event) {
			if (this.colorPickerOpen === false) {
				document.body.classList.add('znpb-color-gradient--backdrop')
				this.rafMovePosition = rafSchd(this.onCircleDrag)
				this.rafEndDragging = rafSchd(this.disableDragging)

				document.addEventListener('mousemove', this.rafMovePosition)
				document.addEventListener('mouseup', this.rafEndDragging)
				document.body.style.userSelect = 'none'
				this.draggedCircleIndex = colorConfigIndex
				this.draggedItem = this.computedValue.colors[colorConfigIndex]
				this.deletedColorConfig = null
			}
		},
		disableDragging (event) {
			document.body.classList.remove('znpb-color-gradient--backdrop')
			document.removeEventListener('mousemove', this.rafMovePosition)
			document.removeEventListener('mouseup', this.rafEndDragging)
			document.body.style.userSelect = null
			this.deletedColorConfig = null
			this.draggedCircleIndex = null
		},
		updateActiveConfigPosition (newPosition) {
			const newConfig = {
				...this.activeDraggedItem,
				position: newPosition
			}

			this.onColorConfigUpdate(this.activeDraggedItem, newConfig)
		},
		onCircleDrag (event) {
			// calculate the dragger left position %
			let newLeft = ((event.clientX - this.gradref.left) * 100) / this.gradref.width
			const position = Math.min(Math.max(newLeft, 0), 100)

			// check if the user wants to delete the color as in photoshop
			if (newLeft > 100 || newLeft < 0) {
				// Check to see if we need to delete the color
				if (this.sortedColors.length > 2 && this.deletedColorConfig === null) {
					this.onDeleteColor(this.draggedItem)
				}
			} else {
				if (this.deletedColorConfig !== null) {
					this.reAddColor()
				}

				this.$nextTick(() => {
					// Update position
					this.updateActiveConfigPosition(Math.round(position))
				})
			}
		}
	},
	beforeUnmount () {
		document.body.classList.remove('znpb-color-gradient--backdrop')
		this.disableDragging()
	}
}
</script>
<style lang="scss">
.znpb-gradient-bar-wrapper {
	position: relative;
	height: 100%;
	.znpb-gradient-preview {
		max-height: 25px;
		margin-bottom: 0;
		border-radius: 3px;
	}
}
.znpb-gradient-bar-colors-wrapper {
	display: flex;
	flex-direction: column;
}

.znpb-gradient-colors-legend {
	display: flex;

	&-item {
		flex: 1;

		&:last-child {
			margin-left: 20px;
		}
	}
}
</style>
