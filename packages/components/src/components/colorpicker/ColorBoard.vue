<template>
	<div class="znpb-form-colorpicker-saturation">
		<div
			ref="boardContent"
			:style="{background: bgColor}"
			@mousedown="initiateDrag"
			@mouseup="deactivatedragCircle"
			class="znpb-form-colorpicker-saturation__color"
		>
			<div class="znpb-form-colorpicker-saturation__white">
				<div class="znpb-form-colorpicker-saturation__black">
					<div
						:style="this.isDragging ? null : pointStyles"
						ref="boardPointer"
						class="znpb-color-picker-pointer"
					></div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import rafSchd from 'raf-schd'

export default {
	name: 'ColorBoard',
	props: {
		modelValue: {
			type: Object,
			required: false
		}
	},
	data () {
		return {
			initialX: null,
			initialY: null,
			isDragging: false
		}
	},
	computed: {
		/**
		 * Returns the position of the pointer
		 */
		pointStyles () {
			const { v, s } = this.modelValue.hsva
			const cssStyles = {
				top: 100 - (v * 100) + '%',
				left: s * 100 + '%'
			}
			return cssStyles
		},
		/**
		 * Returns the background of the colorBoard
		 */
		bgColor () {
			const { h } = this.modelValue.hsva
			return `hsl(${h}, 100%, 50%)`
		},
		boardContent () {
			return this.$refs.boardContent.getBoundingClientRect()
		},
		pointer () {
			return this.$refs.boardPointer.getBoundingClientRect()
		}

	},

	methods: {
		initiateDrag (event) {
			this.isDragging = true
			let { clientX, clientY } = event
			this.rafDragCircle = rafSchd(this.dragCircle)
			window.addEventListener('mousemove', this.rafDragCircle)
			window.addEventListener('mouseup', this.deactivatedragCircle)

			/// Emit click value
			let newLeft = clientX - this.boardContent.left
			let newTop = clientY - this.boardContent.top
			let bright = 100 - ((newTop / this.boardContent.height) * 100)
			let saturation = (newLeft * 100) / this.boardContent.width

			let newColor = {
				...this.modelValue.hsva,
				v: bright / 100,
				s: saturation / 100
			}

			this.$emit('update:modelValue', {
				...newColor,
				type: 'hsva'
			})

			const movedX = clientX - this.initialX - this.boardContent.left
			const movedY = clientY + this.initialY - this.boardContent.top - (this.boardContent.height / 2)
			this.translatePointer(movedX, movedY)
		},
		deactivatedragCircle () {
			window.removeEventListener('mousemove', this.rafDragCircle)
			window.removeEventListener('mouseup', this.deactivatedragCircle)
		},
		translatePointer (x, y) {
			this.$refs.boardPointer.style.transform = `translate3d(${x - this.pointer.width / 2}px, ${y - this.pointer.width / 2}px, 0)`
		},
		dragCircle (event) {
			// If the mouseup happened outside window
			if (!event.which) {
				this.deactivatedragCircle()
				return false
			}
			let { clientX, clientY } = event

			let movedX = clientX - this.initialX - this.boardContent.left
			let movedY = clientY + this.initialY - this.boardContent.top - (this.boardContent.height / 2)
			if (clientX < this.boardContent.left) {
				movedX = 0
			}
			if (clientX > this.boardContent.left + this.boardContent.width) {
				movedX = this.boardContent.width
			}
			if (clientY < this.boardContent.top) {
				movedY = -this.boardContent.height / 2
			}
			if (clientY > this.boardContent.top + this.boardContent.height) {
				movedY = this.boardContent.height / 2
			}
			this.translatePointer(movedX, movedY)
			// don't let the circle outside hue area
			let newLeft = clientX - this.boardContent.left
			if (newLeft > this.boardContent.width) {
				newLeft = this.boardContent.width
			} else if (newLeft < 0) {
				newLeft = 0
			}

			let newTop = clientY - this.boardContent.top

			// don't let the circle outside hue area
			if (newTop >= this.boardContent.height) {
				newTop = this.boardContent.height
			} else if (newTop < 0) {
				newTop = 0
			}

			let bright = 100 - ((newTop / this.boardContent.height) * 100)
			let saturation = (newLeft * 100) / this.boardContent.width

			let newColor = {
				...this.modelValue.hsva,
				v: bright / 100,
				s: saturation / 100
			}

			this.$emit('update:modelValue', {
				...newColor,
				type: 'hsva'
			})
		}
	},
	created () {
		document.body.classList.add('znpb-color-picker--backdrop')
	},
	beforeUnmount () {
		document.body.classList.remove('znpb-color-picker--backdrop')
		this.deactivatedragCircle()
	}
}
</script>
<style lang="scss">
.znpb-form-colorpicker-saturation {
	position: relative;
	overflow: hidden;
	height: 180px;
	border-top-right-radius: 3px;
	border-top-left-radius: 3px;

	&__color, &__white, &__black {
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
	}
	&__white {
		background: linear-gradient(to right, #fff, rgba(255, 255, 255, 0));
	}
	&__black {
		background: linear-gradient(to top, #000, rgba(0, 0, 0, 0));
	}
}
.znpb-color-picker-pointer {
	position: absolute;
	top: 50%;
	z-index: 1;
	width: 12px;
	height: 12px;
	margin: 0 auto;
	background: #fff;
	border-radius: 50%;
	transform: translate(-50%, -50%);
	cursor: move;
}
</style>
