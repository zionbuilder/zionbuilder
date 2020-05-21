<template>
	<div
		class="znpb-colorpicker-inner-editor__opacity-wrapper"
		@click="dragCircle"
		@mousedown="actCircleDrag">
		<div class="znpb-colorpicker-inner-editor__opacity">
			<div
				:style="barStyles"
				ref="opacitystrip"
				class="znpb-colorpicker-inner-editor__opacity-strip"
			>
			</div>
			<span
				:style="opacityStyles"
				@mousedown="actCircleDrag()"
				class="znpb-colorpicker-inner-editor__opacity-indicator"
			></span>
		</div>
	</div>
</template>
<script>
import tinycolor from 'tinycolor2'

export default {
	name: 'OpacityStrip',
	props: {
		value: {
			type: Object,
			required: false
		}
	},
	data () {
		return {
			stripOffset: {},
			lastA: null
		}
	},
	computed: {
		opacityStyles () {
			return {
				left: (this.value.a * 100) + '%'
			}
		},
		barStyles () {
			let color = tinycolor(this.value)
			return {
				'background-image': 'linear-gradient(to right, rgba(255, 0, 0, 0),' + color.toHexString() + ')'
			}
		}
	},
	methods: {
		actCircleDrag () {
			window.addEventListener('mousemove', this.dragCircle)
			window.addEventListener('mouseup', this.deactivatedragCircle)
		},
		deactivatedragCircle () {
			window.removeEventListener('mousemove', this.dragCircle)
			window.removeEventListener('mouseup', this.deactivatedragCircle)
		},
		dragCircle (event) {
			// If the mouseup happened outside window
			if (!event.which) {
				this.deactivatedragCircle()
				return false
			}

			let a
			const mouseLeftPosition = event.clientX
			const stripOffset = this.$refs.opacitystrip.getBoundingClientRect()

			// Calculate where the coordinate x of the element starts
			const startx = stripOffset.left
			// from total width reduce the coordinate x value
			const newLeft = mouseLeftPosition - startx

			// don't let the circle outside opacity strip
			if (newLeft > (stripOffset.width)) {
				a = 1
			} else if (newLeft < 0) {
				a = 0
			} else {
				a = (newLeft / (stripOffset.width))
				a = a.toFixed(2)
			}

			let newColor = {
				...this.value,
				a: a
			}
			if (this.lastA !== a) {
				this.$emit('input', newColor)
			}

			this.lastA = a
		}

	},
	beforeDestroy () {
		this.deactivatedragCircle()
	},
	destroyed () {
		window.removeEventListener('mousemove', this.dragCircle)
	}
}
</script>
<style lang="scss">
.znpb-colorpicker-inner-editor__opacity {
	@extend %opacitybg;

	&-indicator {
		@extend %hue-indicator-style;
	}
	&-strip {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-image: linear-gradient(to right, rgba(255,0,0,0), rgba(255,0,0,1));
	}
}
</style>
