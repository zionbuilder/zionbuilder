<template>
	<div class=" ">
		<ul class="zion-inline-editor__font-panel znpb-fancy-scrollbar">
			<li
				v-for="(font, i) in fontsListForOption"
				:key="i"
				class="zion-inline-editor__font-list-item"
				:class="{ 'zion-inline-editor__font-list-item--active': isActive(font.id) }"
				@click="changeFont(font.id)"
			>
				{{ font.name }}
			</li>
		</ul>
	</div>
</template>

<script lang="ts" setup>
import { ref, inject, onMounted, onBeforeUnmount } from 'vue';
const { useDataSetsStore } = window.zb.store;

const editor = inject('ZionInlineEditor');
const { fontsListForOption } = useDataSetsStore();
const activeFont = ref('');

function isActive(fontName: string) {
	return activeFont.value === fontName ? 'zion-inline-editor__font-list-item--active' : '';
}

function onNodeChange() {
	getFontName();
}

function changeFont(font: string) {
	activeFont.value = font;

	editor.editor.formatter.toggle('fontname', {
		value: font,
	});
}

function getFontName() {
	activeFont.value = editor.editor.queryCommandValue('fontname');
}

onMounted(() => {
	getFontName();
	editor.editor.on('SelectionChange', onNodeChange);
});

onBeforeUnmount(() => {
	editor.editor.off('SelectionChange', onNodeChange);
});
</script>

<style lang="scss">
.zion-inline-editor {
	.zion-inline-editor {
		&__font-panel {
			width: 100%;
			padding: 8px 0;
			background: var(--zb-surface-color);
		}

		&__font-list {
			display: block;
			overflow: hidden;
			overflow-y: scroll;
			width: 100%;
			max-height: 150px;
			padding: 0;
			margin: 0;
			list-style: none;

			&-item {
				display: flex;
				align-items: center;
				width: 100%;
				padding: 8px 16px;
				color: var(--zb-surface-text-color);
				font-family: var(--zb-font-stack);
				font-size: 13px;
				font-weight: 500;
				text-align: left;
				transition: all 0.2s;

				&:hover,
				&--active {
					color: var(--zb-surface-text-active-color);
					background-color: var(--zb-surface-lighter-color);
				}
			}
		}
	}
}
</style>
