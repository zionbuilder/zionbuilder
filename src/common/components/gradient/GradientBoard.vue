<template>
	<div ref="gradboard" class="znpb-gradient-wrapper__board">
		<GradientPreview :config="config"></GradientPreview>
		<div v-if="radialArr!=null" class="znpb-gradient-radial-wrapper">
			<GradientRadialDragger
				v-for="(gradient, index) in radialArr"
				:key="gradient.type + index"
				:position="gradient.position"
				:active="activegrad===gradient"
				@mousedown.native="enableDragging(gradient, $event)"
			/>
		</div>
	</div>
</template>
<script>
import GradientPreview from './GradientPreview.vue'
import GradientRadialDragger from './GradientRadialDragger.vue'
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
			document.addEventListener('mousemove', this.onCircleDrag)
			document.addEventListener('mouseup', this.disableDragging)

			document.body.style.userSelect = 'none'

			const activeGradientIndex = this.config.indexOf(gradient)
			// Set new active gradient config
			this.$emit('change-active-gradient', activeGradientIndex)
		},
		disableDragging (event) {
			document.removeEventListener('mousemove', this.onCircleDrag)
			document.removeEventListener('mouseup', this.disableDragging)

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
	beforeDestroy () {
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
