<template>
	<div
		class="znpb-colorpicker-inner-editor__opacity-wrapper"
		@mousedown="actCircleDrag"
		ref="root"
	>
		<div
			class="znpb-colorpicker-inner-editor__opacity"
			@click="dragCircle"
		>
			<div
				:style="barStyles"
				ref="opacitystrip"
				class="znpb-colorpicker-inner-editor__opacity-strip"
			>
			</div>
			<span
				:style="opacityStyles"
				@mousedown="actCircleDrag"
				class="znpb-colorpicker-inner-editor__opacity-indicator"
			></span>
		</div>
	</div>
</template>
<script>
import tinycolor from 'tinycolor2'
import rafSchd from 'raf-schd'
import { onMounted, onBeforeUnmount, ref, computed } from 'vue'

export default {
	name: 'OpacityStrip',
	props: {
		modelValue: {
			type: Object,
			required: false
		}
	},
	setup (props, { emit }) {
		// Refs
		const root = ref(null)
		const opacitystrip = ref(null)

		// Temp values
		const rafDragCircle = rafSchd(dragCircle)
		let lastA = null
		let ownerWindow = null

		// Computed properties
		const opacityStyles = computed(() => {
			return {
				left: (props.modelValue.a * 100) + '%'
			}
		})

		const barStyles = computed(() => {
			let color = tinycolor(props.modelValue)
			return {
				'background-image': 'linear-gradient(to right, rgba(255, 0, 0, 0),' + color.toHexString() + ')'
			}
		})

		// Methods
		function actCircleDrag () {
			ownerWindow.addEventListener('mousemove', rafDragCircle)
			ownerWindow.addEventListener('mouseup', deactivatedragCircle)
		}

		function deactivatedragCircle () {
			ownerWindow.removeEventListener('mousemove', rafDragCircle)
			ownerWindow.removeEventListener('mouseup', deactivatedragCircle)
		}

		function dragCircle (event) {
			// If the mouseup happened outside window
			if (!event.which) {
				deactivatedragCircle()
				return false
			}

			let a
			const mouseLeftPosition = event.clientX

			const stripOffset = opacitystrip.value.getBoundingClientRect()

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
				...props.modelValue,
				a: a
			}

			if (lastA !== a) {
				emit('update:modelValue', newColor)
			}

			lastA = a
		}

		// Lifecycle
		onMounted(() => {
			ownerWindow = root.value.ownerDocument.defaultView
		})

		onBeforeUnmount(() => {
			deactivatedragCircle()
		})

		return {
			// Refs
			root,
			opacitystrip,
			// commputed
			opacityStyles,
			barStyles,
			actCircleDrag,
			dragCircle
		}
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
		background-image: linear-gradient(
		to right,
		rgba(255, 0, 0, 0),
		rgba(255, 0, 0, 1)
		);
	}
}
</style>
