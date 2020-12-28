<template>
	<div
		class="zion-inline-editor-slider-area"
		@click.stop=""
	>
		<InputRangeDynamic
			@update:modelValue="onFontChange"
			:modelValue="sliderValue"
			:options="options"
			@click.stop="onClick"
			class="zion-inline-editor-slider-area--slider"
			ref="inputRangeDynamic"
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
			unitsExpanded: null,
			options: [
				{
					unit: 'px',
					min: 6,
					max: 300,
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
					unit: 'rem',
					min: 1,
					max: 6,
					step: 1,
					shiftStep: 1
				},
				{
					unit: 'pt',
					min: 1,
					max: 60,
					step: 1,
					shiftStep: 1
				},
				{
					unit: 'vh',
					min: 1,
					max: 100,
					step: 5,
					shiftStep: 1
				}
			],
			sliderValue: null,
			justChangedNode: null
		}
	},

	beforeMount: function () {
		this.Editor.editor.on('NodeChange', this.onNodeChange)
		let nodeDom = this.Editor.$refs.inlineEditor
		let selectedNode = this.Editor.editor.selection.getNode()

		if (nodeDom.contains(selectedNode)) {
			this.getFontSize(selectedNode)
		} else {
			this.Editor.editor.execCommand('mceSelectNode', false, nodeDom.firstChild)
			this.getFontSize(nodeDom.firstChild)
		}

	},
	beforeUnmount () {
		this.Editor.editor.off('NodeChange', this.onNodeChange)
	},
	methods: {
		onClick (e) {
			this.$emit('units-expanded', this.$refs.inputRangeDynamic ? this.$refs.inputRangeDynamic.$refs.InputNumberUnit.expanded : null)
		},
		onFontChange (newValue) {
			this.Editor.editor.formatter.apply('fontsize', { value: newValue })
			this.justChangedNode = true
			this.sliderValue = newValue
			this.$emit('started-dragging')
			this.$emit('units-expanded', this.$refs.inputRangeDynamic ? this.$refs.inputRangeDynamic.$refs.InputNumberUnit.expanded : null)
			this.$refs.inputRangeDynamic.$refs.InputNumberUnit.$refs.numberUnitInput.$refs.input.focus()
		},
		onNodeChange (node) {
			if (node.selectionChange && !this.justChangedNode) {
				this.getFontSize(node.element)
			}

			this.justChangedNode = false
		},
		getFontSize (node) {

			let fontSize = this.Editor.editor.queryCommandValue('FontSize')
			this.sliderValue = fontSize
		}
	}

}
</script>

<style lang="scss">
.zion-inline-editor-slider-area {
	width: 100%;
	padding: 16px;
	font-size: 13px;
}
</style>
