<template>
	<div
		class="zion-inline-editor-slider-area"
		@click.stop=""
	>
		<InputRangeDynamic
			@input="onLetterChange"
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
			sliderValue: null,
			justChangedNode: null,
			options: [
				{
					unit: 'px',
					min: 0,
					max: 300,
					step: 1,
					shiftStep: 5
				},
				{
					unit: 'rem',
					min: 1,
					max: 10,
					step: 1,
					shiftStep: 1
				},
				{
					unit: 'normal',
					min: null,
					max: null,
					step: null,
					shiftStep: null
				}

			]
		}
	},
	beforeMount: function () {
		this.Editor.editor.on('NodeChange', this.onNodeChange)
		this.Editor.editor.formatter.register('letterSpacing', {
			inline: 'span',
			styles: { 'letter-spacing': '%value' }
		})
		// Set default letter spacing
		this.getLetterSpacing(this.Editor.editor.selection.getNode())
	},
	beforeUnmount () {
		this.Editor.editor.off('NodeChange', this.onNodeChange)
	},
	methods: {
		onClick (e) {
			this.$emit('units-expanded', this.$refs.inputRangeDynamic ? this.$refs.inputRangeDynamic.$children[1].expanded : null)
		},
		onLetterChange (newValue) {
			this.Editor.editor.formatter.apply('letterSpacing', { value: newValue })
			this.justChangedNode = true
			this.sliderValue = newValue
			this.$emit('started-dragging')
			this.$emit('units-expanded', this.$refs.inputRangeDynamic ? this.$refs.inputRangeDynamic.$children[1].expanded : null)
			this.$refs.inputRangeDynamic.$refs.InputNumberUnit.$refs.numberUnitInput.$refs.input.focus()
		},
		onNodeChange (node) {
			if (node.selectionChange && !this.justChangedNode) {
				this.getLetterSpacing(node.element)
			}
			this.justChangedNode = false
		},
		getLetterSpacing (node) {
			let letterSpacing = window.getComputedStyle(node).getPropertyValue('letter-spacing')
			this.sliderValue = letterSpacing
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
