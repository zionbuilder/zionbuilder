<template>
	<div
		ref="root"
		@click="dragHueCircle"
		class="znpb-colorpicker-inner-editor__hue-wrapper"
	>
		<div class="znpb-colorpicker-inner-editor__hue">
			<span
				:style="hueStyles"
				@mousedown="actHueCircleDrag"
				class="znpb-colorpicker-inner-editor__hue-indicator"
			/>
		</div>
	</div>
</template>
<script>
import rafSchd from 'raf-schd'

export default {
	name: 'HueStrip',
	props: {
		modelValue: {
			type: Object,
			required: false
		}
	},
	data () {
		return {
			direction: 'right',
			oldHue: this.modelValue.h,
			lastHue: null,
			ownerWindow: null
		}
	},
	computed: {
		hueStyles () {
			const { h } = this.modelValue
			let positionValue = (this.modelValue.h / 360) * 100

			if (h === 0 && this.direction === 'right') {
				positionValue = 100
			}

			return {
				left: positionValue + '%'
			}
		}
	},
	watch: {
		modelValue (newValue) {
			const { h } = this.modelValue
			if (h !== 0 && h > this.oldHue) { this.direction = 'right' }
			if (h !== 0 && h < this.oldHue) { this.direction = 'left' }

			this.oldHue = h
		}
	},
	methods: {
		actHueCircleDrag () {
			this.rafDragCircle = rafSchd(this.dragHueCircle)
			this.ownerWindow.addEventListener('mousemove', this.rafDragCircle)
			this.ownerWindow.addEventListener('mouseup', this.deactivatedragHueCircle)
		},
		deactivatedragHueCircle (event) {
			this.ownerWindow.removeEventListener('mousemove', this.rafDragCircle)
			this.ownerWindow.removeEventListener('mouseup', this.deactivatedragHueCircle)

			function preventClicks (e) {
				e.stopPropagation()
			}

			// Prevent closing colorpicker when clicked outside
			this.ownerWindow.addEventListener('click', preventClicks, true)
			setTimeout(() => {
				this.ownerWindow.removeEventListener('click', preventClicks, true)
			}, 100);
		},
		isMouseUpOutsideWindow (event) {
			if (!event.which) {
				return
			}

			return false
		},
		dragHueCircle (event) {
			// If the mouseup happened outside window
			if (!event.which) {
				this.deactivatedragHueCircle()
				return false
			}

			let h
			const mouseLeftPosition = event.clientX
			const stripOffset = this.$el.getBoundingClientRect()

			// Calculate where the coordinate x of the element starts
			const startx = stripOffset.left
			// from total width reduce the coordinate x value
			const newLeft = mouseLeftPosition - startx
			// don't let the circle outside opacity strip
			if (newLeft > stripOffset.width) {
				h = 360
			} else if (newLeft < 0) {
				h = 0
			} else {
				let percent = ((newLeft) * 100) / stripOffset.width
				h = (360 * percent / 100)
			}

			let newColor = {
				...this.modelValue,
				h: h
			}

			if (this.lastHue !== h) {
				this.$emit('update:modelValue', newColor)
			}

			this.lastHue = h


		}

	},
	mounted () {
		this.ownerWindow = this.$refs.root.ownerDocument.defaultView
	},
	beforeUnmount () {
		this.deactivatedragHueCircle()
	},
	unmounted () {
		this.ownerWindow.removeEventListener('mousemove', this.dragHueCircle)
	}
}
</script>
<style lang="scss">
.znpb-colorpicker-inner-editor__hue {
	background-image: linear-gradient(
	to right,
	red 0%,
	#ff4d00 5%,
	#f90 10%,
	#ffe600 15%,
	#cf0 20%,
	#80ff00 25%,
	#3f0 30%,
	#00ff1a 35%,
	#0f6 40%,
	#00ffb3 45%,
	cyan 50%,
	#06f 60%,
	#001aff 65%,
	#30f 70%,
	#8000ff 75%,
	#c0f 80%,
	#ff00e6 85%,
	#f09 90%,
	#ff004d 95%,
	red 100%
	);

	&-wrapper {
		margin-bottom: 14px;
	}

	&-indicator {
		@extend %hue-indicator-style;
	}
}
</style>
