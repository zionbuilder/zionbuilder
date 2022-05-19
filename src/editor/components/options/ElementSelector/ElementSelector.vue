<template>
	<div class="znpb-option-element-selector">
		<BaseInput v-model="valueModel">
			<template v-slot:append>
				<span @click="activateSelectorMode">Select</span>
			</template>
		</BaseInput>
	</div>
</template>

<script>
import { computed } from 'vue'

export default {
	name: 'ElementSelector',
	props: {
		modelValue: {
			type: String,
			required: false
		},
		use_preview: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	setup (props, { emit }) {
		let activeDoc = document
		let lastElement = null

		const valueModel = computed({
			get () {
				return props.modelValue
			},
			set (newValue) {
				emit('update:modelValue', newValue)
			}
		})

		function activateSelectorMode () {
			if (props.use_preview) {
				const iframeElement = document.getElementById('znpb-editor-iframe')

				if (!iframeElement) {
					console.error('The iframe preview is missing')
					return
				}

				activeDoc = iframeElement.contentWindow.document

			}

			activeDoc.addEventListener('mousemove', onMouseMove)
			activeDoc.body.classList.add('znpb-element-selector--active')
		}

		function disableElementSelector () {
			activeDoc.body.classList.remove('znpb-element-selector--active')
		}

		function onMouseMove (event) {
			// let activeElement = document.elementFromPoint(event.clientX, event.clientY)
			// console.log(activeElement);
			const { clientX, clientY } = event

			// Cleanup
			if (lastElement) {
				lastElement.classList.remove('znpb-element-selector--element-hovered')
			}


			lastElement = activeDoc.elementFromPoint(clientX, clientY);

			// Highlight hovered element
			lastElement.classList.add('znpb-element-selector--element-hovered')

			// console.log(el);
			// if (!activeElement) {
			// 	console.log('not active');
			// 	const iframeElement = document.getElementById('znpb-editor-iframe')

			// 	if (iframeElement) {
			// 		console.log('we have iframe');
			// 		const iframeDoc = iframeElement.contentWindow.document
			// 		activeElement = iframeDoc.elementFromPoint(event.clientX, event.clientY)
			// 	}
			// }

			// console.log(activeElement);
		}

		return {
			valueModel,
			activateSelectorMode
		}
	}
}
</script>

<style lang="scss">
// Hide element toolboxes
.znpb-element-selector--active .znpb-element-toolbox {
	display: none !important;
}

// Disable selection of inline text editor
.znpb-element-selector--active .znpb-inline-text-editor {
	pointer-events: none !important;
}

// Highlight hovered selector
.znpb-element-selector--element-hovered {
	outline: 1px solid red;
}
</style>