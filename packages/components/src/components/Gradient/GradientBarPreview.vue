<template>
	<div
		class="znpb-gradient-preview-transparent-container"
		:title="$translate('click_to_add_gradient_point')"
	>
		<div class="znpb-gradient-preview-transparent">
			<div
				class="znpb-gradient-preview"
				:style="getGradientPreviewStyle"
			>
			</div>
		</div>
	</div>
</template>
<script>

export default {
	name: 'GradientBarPreview',
	props: {
		config: {
			type: Object,
			required: false
		}
	},
	computed: {
		getGradientPreviewStyle () {
			let style = {}
			let gradient = []
			let colors = []

			const colorsCopy = [...this.config.colors]

			colorsCopy.sort((a, b) => {
				return a.position > b.position ? 1 : -1
			})

			colorsCopy.forEach((color) => {
				colors.push(`${color.color} ${color.position}%`)
			})

			gradient.push(`linear-gradient(90deg, ${colors.join(', ')})`)

			style['background'] = gradient.join(', ')

			return style
		}
	}
}
</script>
<style lang="scss">
.znpb-gradient-preview-transparent-container {
	margin-bottom: 25px;
	.znpb-gradient-preview-transparent {
		@extend %opacitybg;
		box-shadow: none;
	}
}
</style>
