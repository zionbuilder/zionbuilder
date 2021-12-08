<template>
	<div
		class="znpb-gradient-preview-transparent"
		:class="{'gradient-type-rounded' : round}"
	>
		<div
			class="znpb-gradient-preview"
			:style="getGradientPreviewStyle"
			:class="{'gradient-type-rounded' : round}"
		></div>
	</div>
</template>
<script>
import { applyFilters } from '@zb/hooks'

export default {
	name: 'GradientPreview',
	props: {
		config: {
			type: Array,
			required: false
		},
		round: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {}
	},
	computed: {
		// Filter the value so we can set the dynamic colors
		filteredConfig () {
			return applyFilters('zionbuilder/options/model', JSON.parse(JSON.stringify(this.config)))
		},
		getGradientPreviewStyle () {
			let style = {}
			let gradient = []

			this.filteredConfig.forEach(element => {
				let colors = []
				let position = '90deg'

				const colorsCopy = [...element.colors].sort((a, b) => {
					return a.position > b.position ? 1 : -1
				})

				colorsCopy.forEach((color) => {
					colors.push(`${color.color} ${color.position}%`)
				})

				// Set position
				if (element.type === 'radial') {
					const { x, y } = element.position || { x: 50, y: 50 }
					position = `circle at ${x}% ${y}%`
				} else {
					position = `${element.angle}deg`
				}

				gradient.push(`${element.type}-gradient(${position}, ${colors.join(', ')})`)
			})
			gradient.reverse()

			style['background-image'] = gradient.join(', ')

			return style
		}
	}
}
</script>
<style lang="scss">
.znpb-gradient-preview-transparent {
	@extend %opacitybg;
	box-shadow: 0 0 0 2px var(--zb-surface-color) inset,
	0 0 0 1px var(--zb-surface-color), 0 0 2px var(--zb-surface-color);

	&.gradient-type-rounded {
		width: 46px;
		height: 46px;
		border-radius: 3px;
	}
}
.znpb-gradient-preview {
	width: 100%;
	height: 200px;
	border-radius: 0;
	&.gradient-type-rounded {
		width: 46px;
		height: 46px;
		margin-bottom: 18px;
		border-radius: 3px;
		cursor: pointer;
	}
}
</style>
