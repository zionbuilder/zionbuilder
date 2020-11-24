<template>
	<div class="znpb-gradient-preview-transparent">
		<div class="znpb-gradient-preview" :style="getGradientPreviewStyle" :class="{'gradient-type-rounded' : round}"></div>
	</div>
</template>
<script>

export default {
	name: 'OneGradient',
	props: {
		config: {
			type: Object,
			required: false
		},
		round: {
			type: Boolean,
			required: false,
			default: false
		}

	},
	data () {
		return {
			localConfig: this.config
		}
	},
	computed: {
		getGradientPreviewStyle () {
			let style = {}
			let gradient = []
			let colors = []
			let position = '90deg'

			const colorsCopy = [...this.config.colors].sort((a, b) => {
				return a.position > b.position ? 1 : -1
			})

			colorsCopy.forEach((color) => {
				colors.push(`${color.color} ${color.position}%`)
			})

			// Set position
			if (this.config.type === 'radial') {
				const { x, y } = this.config.position || { x: 50, y: 50 }
				position = `circle at ${x}% ${y}%`
			} else {
				position = `${this.config.angle}deg`
			}

			gradient.push(`${this.config.type}-gradient(${position}, ${colors.join(', ')})`)
			style['background'] = gradient.join(', ')

			return style
		}
	}
}
</script>
<style lang="scss" scoped>
.znpb-gradient-preview-transparent {
	@extend %opacitybg;
	width: 100%;
	height: 100%;
}
</style>
