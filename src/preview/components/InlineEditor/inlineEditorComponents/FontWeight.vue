<template>
	<PopOver icon="ite-weight" :is-active="isActive" :full-size="true">
		<InlineEditorButton
			v-for="fontWeight in fontWeights"
			:key="fontWeight"
			formatter="fontWeight"
			:formatter-value="fontWeight"
			:buttontext="fontWeight"
		/>
	</PopOver>
</template>

<script lang="ts" setup>
import { ref, inject, onBeforeMount, onBeforeUnmount } from 'vue';

// Components
import PopOver from './PopOver.vue';
import InlineEditorButton from './Button.vue';

const editor = inject('ZionInlineEditor');
const isActive = ref(false);
const fontWeights = [100, 200, 300, 400, 500, 600, 700, 800, 900];

function checkIfActive() {
	isActive.value = fontWeights.some(fontWeight => {
		return editor.editor.formatter.match('fontWeight', { value: fontWeight });
	});
}

onBeforeMount(() => {
	checkIfActive();
	editor.editor.on('NodeChange', checkIfActive);
});

onBeforeUnmount(() => {
	editor.editor.off('NodeChange', checkIfActive);
});
</script>

<style lang="scss">
.zion-inline-editor-button {
	padding: 6px;
	font-size: 13px;
	font-weight: 500;
	line-height: 25px;
}
</style>
