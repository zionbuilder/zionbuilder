<template>
	<div class="znpb-option-element-selector">
		<BaseInput v-model="valueModel">
			<template #append>
				<span @click="activateSelectorMode">Select</span>
			</template>
		</BaseInput>
	</div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		// eslint-disable-next-line vue/prop-name-casing
		use_preview: boolean;
	}>(),
	{
		modelValue: '',
		use_preview: true,
	},
);

const emit = defineEmits(['update:modelValue']);

let activeDoc = document;
let lastElement: Element | null = null;

const valueModel = computed({
	get() {
		return props.modelValue;
	},
	set(newValue) {
		emit('update:modelValue', newValue);
	},
});

function activateSelectorMode() {
	if (props.use_preview) {
		const iframeElement = <HTMLIFrameElement>document.getElementById('znpb-editor-iframe');

		if (!iframeElement || !iframeElement.contentWindow) {
			console.error('The iframe preview is missing');
			return;
		}

		activeDoc = iframeElement.contentWindow.document;
	}

	activeDoc.addEventListener('mousemove', onMouseMove);
	activeDoc.body.classList.add('znpb-element-selector--active');
}

function disableElementSelector() {
	activeDoc.body.classList.remove('znpb-element-selector--active');
}

function onMouseMove(event: MouseEvent) {
	const { clientX, clientY } = event;

	// Cleanup
	if (lastElement) {
		lastElement.classList.remove('znpb-element-selector--element-hovered');
	}

	lastElement = activeDoc.elementFromPoint(clientX, clientY);

	// Highlight hovered element
	if (lastElement) {
		lastElement.classList.add('znpb-element-selector--element-hovered');
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
