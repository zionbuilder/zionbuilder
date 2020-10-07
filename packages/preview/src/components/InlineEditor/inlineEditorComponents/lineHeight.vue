<template>
	<div
		class="zion-inline-editor-slider-area"
		@click.stop=""
	>

		<InputRangeDynamic
			@input="onHeightChange"
			:modelValue="sliderValue"
			:options="options"
			class="zion-inline-editor-slider-area--slider"
			ref="inputRangeDynamic"
			@click="onClick"
		/>

	</div>
</template>

<script>
export default {
	inject: {
		Editor: {
			default () {
				return {}
			}
		}
	},
	data () {
		return {

			options: [
				{
					unit: 'px',
					min: 1,
					max: 400,
					step: 1,
					shiftStep: 5
				},

				{
					unit: 'em',
					min: 1,
					max: 100,
					step: 1,
					shiftStep: 5
				},
				{
					unit: '%',
					min: 1,
					max: 100,
					step: 1,
					shiftStep: 5
				},
				{
					unit: 'normal',
					min: null,
					max: null,
					step: null,
					shiftStep: null
				}
			],
			sliderValue: null,
			justChangedNode: null
		}
	},

	beforeMount: function () {
		this.Editor.editor.on('NodeChange', this.onNodeChange)
		// // Set default line height
		// this.getLineHeight(this.Editor.editor.selection.getNode())
		this.Editor.editor.formatter.register('lineHeight', {
			inline: 'span',
			styles: { 'line-height': '%value' }
		})
		// Set default line height
		this.getLineHeight(this.Editor.editor.selection.getNode())
	},

	beforeUnmount () {
		this.Editor.editor.off('NodeChange', this.onNodeChange)
	},
	methods: {
		onClick (e) {
			this.$emit('units-expanded', this.$refs.inputRangeDynamic ? this.$refs.inputRangeDynamic.$children[1].expanded : null)
		},
		onHeightChange (newValue) {
			this.Editor.editor.formatter.apply('lineHeight', { value: newValue })
			this.justChangedNode = true
			this.sliderValue = newValue
			this.$emit('started-dragging')
			this.$emit('units-expanded', this.$refs.inputRangeDynamic ? this.$refs.inputRangeDynamic.$children[1].expanded : null)
			this.$refs.inputRangeDynamic.$refs.InputNumberUnit.$refs.numberUnitInput.$refs.input.focus()
		},
		onNodeChange (node) {
			if (node.selectionChange && !this.justChangedNode) {
				this.getLineHeight(node.element)
			}
			this.justChangedNode = false
		},

		getLineHeight (node) {
			// commnad not supported
			this.sliderValue = window.getComputedStyle(node).getPropertyValue('line-height')
		}
	}
}
</script>

<style>
.zion-inline-editor-slider-area {
	width: 100%;
	padding: 16px;
	font-size: 13px;
}
</style>
