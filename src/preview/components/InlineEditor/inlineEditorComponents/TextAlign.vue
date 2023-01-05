<template>
	<PopOver icon="ite-alignment" :is-active="isActive">
		<InlineEditorButton
			v-for="button in buttons"
			:key="button.formatter"
			:formatter="button.formatter"
			:icon="button.icon"
		/>
	</PopOver>
</template>

<script lang="ts" setup>
import { ref, inject, onBeforeMount, onBeforeUnmount } from 'vue';

// Components
import InlineEditorButton from './Button.vue';
import PopOver from './PopOver.vue';

const editor = inject('ZionInlineEditor');
const isActive = ref(false);

const buttons = [
	{
		formatter: 'alignleft',
		icon: 'align--left',
	},
	{
		formatter: 'aligncenter',
		icon: 'align--center',
	},
	{
		formatter: 'alignright',
		icon: 'align--right',
	},
	{
		formatter: 'alignjustify',
		icon: 'align--justify',
	},
];

function checkIfActive() {
	isActive.value =
		editor.editor.formatter.matchAll(['alignleft', 'aligncenter', 'alignright', 'alignjustify']).length > 0;
}

onBeforeMount(() => {
	checkIfActive();
	editor.editor.on('NodeChange', checkIfActive);
});

onBeforeUnmount(() => {
	editor.editor.off('NodeChange', checkIfActive);
});
</script>
