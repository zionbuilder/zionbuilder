<template>
	<div class="zion-inline-editor-slider-area">
		<InputRangeDynamic
			@update:modelValue="onLetterChange"
			:modelValue="sliderValue"
			:options="options"
			@click.stop="onClick"
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
		const sliderValue = ref(null)
		const inputRangeDynamicRef = ref(null)
		let isCurrentChange = false
		let changeTimeout = null

		const options = [
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

		function onClick (e) {
			emit('units-expanded', inputRangeDynamicRef.value ? inputRangeDynamicRef.value.$refs.InputNumberUnit.expanded : null)
		}

		function onLetterChange (newValue) {
			editor.editor.formatter.apply('letterSpacing', { value: newValue })
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
				getLetterSpacing()
			}
		}

		function getLetterSpacing () {
			let letterSpacing = window.getComputedStyle(editor.editor.selection.getNode()).getPropertyValue('letter-spacing')
			sliderValue.value = letterSpacing
		}

		onMounted(() => {
			editor.editor.formatter.register('letterSpacing', {
				selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
				styles: { 'letter-spacing': '%value' }
			})


			getLetterSpacing()
			editor.editor.on('SelectionChange', onNodeChange)
		})

		onBeforeUnmount(() => {
			editor.editor.off('SelectionChange', onNodeChange)
		})

		return {
			onLetterChange,
			sliderValue,
			options,
			onClick,
			inputRangeDynamicRef
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
