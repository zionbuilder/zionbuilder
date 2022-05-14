<template>
	<div class="zion-inline-editor-slider-area">
		<InputRangeDynamic
			@update:modelValue="onFontChange"
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
		const unitsExpanded = ref(false)
		const sliderValue = ref(null)
		const inputRangeDynamicRef = ref(null)
		let changeTimeout = null
		let isCurrentChange = false

		const options = [
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
		]


		function onClick (e) {
			emit('units-expanded', inputRangeDynamicRef.value ? inputRangeDynamicRef.value.$refs.inputNumberUnit.expanded : null)
		}

		function onFontChange (newValue) {
			editor.editor.formatter.apply('fontsize', { value: newValue })
			sliderValue.value = newValue

			emit('started-dragging')
			emit('units-expanded', inputRangeDynamicRef.value ? inputRangeDynamicRef.value.$refs.inputNumberUnit.expanded : null)

			inputRangeDynamicRef.value.$refs.inputNumberUnit.$refs.numberUnitInput.$refs.input.focus()

			clearTimeout(changeTimeout)
			changeTimeout = setTimeout(() => {
				isCurrentChange = false
			}, 100);

			isCurrentChange = true

		}

		function onNodeChange (node) {
			if (!isCurrentChange) {
				getFontSize()
			}
		}

		function getFontSize () {
			sliderValue.value = editor.editor.queryCommandValue('FontSize')
		}

		onMounted(() => {
			getFontSize()
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
			onFontChange,
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
