<template>
	<div ref="gradboard" class="znpb-gradient-wrapper__board">
		<GradientPreview :config="config"></GradientPreview>
		<div v-if="radialArr!=null" class="znpb-gradient-radial-wrapper">
			<GradientRadialDragger
				v-for="(gradient, index) in radialArr"
				:key="gradient.type + index"
				:position="gradient.position"
				:active="activegrad===gradient"
				@mousedown="enableDragging(gradient, $event)"
			/>
		</div>
	</div>
</template>
<script>
import GradientPreview from './GradientPreview.vue'
import GradientRadialDragger from './GradientRadialDragger.vue'
import rafSchd from 'raf-schd'

export default {
	name: 'GradientBoard',
	components: {
		GradientPreview,
		GradientRadialDragger
	},
	props: {
		config: {
			type: Array,
			required: false
		},
		activegrad: {
			type: Object,
			required: false
		}
	},
	computed: {
		radialArr: {
			get () {
				let radialarr = this.config.filter(gradient => gradient.type === 'radial')
				return	radialarr
			},
			set (newArr) {
				this.radialArr = newArr
			}
		}
	},
	methods: {
		enableDragging (gradient, event) {
			this.rafMovePosition = rafSchd(this.onCircleDrag)
			this.rafEndDragging = rafSchd(this.disableDragging)

			document.addEventListener('mousemove', this.rafMovePosition)
			document.addEventListener('mouseup', this.rafEndDragging)

			document.body.style.userSelect = 'none'

			const activeGradientIndex = this.config.indexOf(gradient)
			// Set new active gradient config
			this.$emit('change-active-gradient', activeGradientIndex)
		},
		disableDragging (event) {
			document.removeEventListener('mousemove', this.rafMovePosition)
			document.removeEventListener('mouseup', this.rafEndDragging)

			document.body.style.userSelect = null
		},
		onCircleDrag (event) {
			let gradBoard = this.$refs.gradboard.getBoundingClientRect()

			// calculate left position
			let newLeft = ((event.clientX - gradBoard.left) * 100) / gradBoard.width

			// check if the dragger goes outside the board
			if (newLeft > 100) {
				newLeft = 100
			} else if (newLeft < 0) {
				newLeft = 0
			}

			// calculate top position
			let newTop = (event.clientY - gradBoard.top) * 100 / gradBoard.height

			// check if the dragger goes outside the board
			if (newTop > 100) {
				newTop = 100
			} else if (newTop < 0) {
				newTop = 0
			}

			this.$emit('position-changed', {
				x: Math.round(newLeft),
				y: Math.round(newTop)
			})
		}
	},
	beforeUnmount () {
		this.disableDragging()
	}
}
</script>
<style lang="scss">

.znpb-gradient-wrapper__board {
	position: relative;
	overflow: hidden;
	margin-bottom: 9px;
	.znpb-gradient-preview-transparent {
		box-shadow: none;
	}
}
</style>
