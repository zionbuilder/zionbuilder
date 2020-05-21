<template>
	<span
		class="znpb-color-picker-pointer"
		:class="{'znpb-color-picker-pointer--active' : active}"
		:style="radialPosition"
	></span>

</template>
<script>

export default {
	name: 'GradientRadialDragger',
	props: {
		position: {
			type: Object,
			required: false
		},
		active: {
			type: Boolean,
			required: false
		}
	},
	data () {
		return {}
	},
	computed: {
		radialPosition () {
			const { x, y } = this.position || { x: 50, y: 50 }
			const cssStyles = {
				left: x + '%',
				top: y + '%'
			}
			return cssStyles
		}
	}
}
</script>
<style lang="scss" scoped>
.znpb-color-picker-pointer {
	width: 8px;
	height: 8px;
	box-shadow: 0 0 15px 0 rgba(0, 0, 0, .4);

	&--active {
		width: 14px;
		height: 14px;
	}
}
</style>
