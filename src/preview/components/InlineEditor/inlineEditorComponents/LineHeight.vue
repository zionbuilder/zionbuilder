<template>
	<div class="zion-inline-editor-slider-area">

		<InputRangeDynamic
			@update:modelValue="onHeightChange"
			:modelValue="sliderValue"
			:options="options"
			class="zion-inline-editor-slider-area--slider"
			ref="inputRangeDynamicRef"
		/>

	</div>
</template>

<script>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue'

export default {
	setup (props, { emit }) {
		const editor = inject('ZionInlineEditor')
		const unitsExpanded = ref(false)
		const sliderValue = ref(null)
		const inputRangeDynamicRef = ref(null)
		let changeTimeout = null
		let isCurrentChange = false

		let options = [
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
		]

		function onClick (e) {
			emit('units-expanded', inputRangeDynamicRef.value ? inputRangeDynamicRef.value.$refs.InputNumberUnit.expanded : null)
		}
		function onHeightChange (newValue) {
			editor.editor.formatter.apply('lineHeight', { value: newValue })

			sliderValue.value = newValue
			emit('started-dragging')
			emit('units-expanded', inputRangeDynamicRef.value ? inputRangeDynamicRef.value.$refs.InputNumberUnit.expanded : null)
			inputRangeDynamicRef.value.$refs.InputNumberUnit.$refs.numberUnitInput.$refs.input.focus()

			clearTimeout(changeTimeout)
			changeTimeout = setTimeout(() => {
				isCurrentChange = false
			}, 100);

			isCurrentChange = true

		}
		function onNodeChange (node) {
			if (!isCurrentChange) {
				getLineHeight()
			}
		}

		function getLineHeight () {
			// commnad not supported
			sliderValue.value = window.getComputedStyle(editor.editor.selection.getNode()).getPropertyValue('line-height')
		}

		onMounted(() => {
			editor.editor.formatter.register('lineHeight', {
				selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
				styles: { 'line-height': '%value' }
			})

			// Set default line height
			getLineHeight()
			editor.editor.on('SelectionChange', onNodeChange)
		})

		onBeforeUnmount(() => {
			editor.editor.off('SelectionChange', onNodeChange)
		})

		return {
			unitsExpanded,
			options,
			sliderValue,
			onClick,
			inputRangeDynamicRef,
			onHeightChange
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
