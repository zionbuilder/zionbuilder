<template>
	<Tooltip
		ref="inlineEditorWrapper"
		class="znpb-inline-editor__wrapper_all"
		tooltip-class="znpb-inline-editor__wrapper hg-popper--no-padding hg-popper--no-bg"
		:trigger="null"
		placement="top"
		append-to="body"
		:show-arrows="false"
		:show="showEditor"
		strategy="fixed"
		:close-on-outside-click="true"
		:hide-on-escape="true"
		@hide="showEditor = false"
	>
		<template #content>
			<div
				v-if="!UIStore.isPreviewMode && tinyMceReady"
				ref="tooltipContentRef"
				class="zion-inline-editor zion-inline-editor-container"
				:class="{ 'zion-inline-editor--dragging': isDragging }"
				:style="barStyles"
				@mousedown="e => e.preventDefault()"
			>
				<!-- Normally positioned drag button -->
				<div
					v-if="dragButtonOnScreen"
					ref="dragButton"
					class="zion-inline-editor-dragButton"
					@mousedown.stop="startDrag"
				>
					<Icon icon="ite-move" />
				</div>

				<!-- Fonts & text style panel -->
				<FontStyle />

				<!-- Bold popover -->
				<FontWeight />

				<!-- Italic button -->
				<InlineEditorButton formatter="italic" icon="ite-italic" />

				<!-- Underline button -->
				<InlineEditorButton formatter="underline" icon="ite-underline" />
				<!-- Uppercase button -->
				<InlineEditorButton formatter="uppercase" icon="ite-uppercase" />

				<!-- Link button -->
				<PanelLink />

				<!-- Quote button -->
				<InlineEditorButton formatter="blockquote" icon="ite-quote" />

				<!-- Color Picker -->
				<ColorPicker @close-color-picker="onColorPickerClose" @open-color-picker="onColorPickerOpen" />

				<!-- Text align button -->
				<TextAlign />

				<!--Alternatively positioned drag button (if the normal one is out of bounds)-->
				<div v-if="!dragButtonOnScreen" class="zion-inline-editor-dragButton" @mousedown.stop="startDrag">
					<Icon icon="more" :rotate="90" />
				</div>
			</div>
		</template>

		<div
			ref="inlineEditorRef"
			class="znpb-inline-text-editor"
			:class="{ 'znpb-inline-text-editor--preview': UIStore.isPreviewMode }"
			:contenteditable="!UIStore.isPreviewMode"
			@mouseup="checkTextSelection"
		/>
	</Tooltip>
</template>

<script lang="ts" setup>
import { ref, computed, toRefs, onMounted, watch, onBeforeUnmount, provide } from 'vue';

// Components
import InlineEditorButton from './inlineEditorComponents/Button.vue';
import ColorPicker from './inlineEditorComponents/ColorPicker.vue';
import FontWeight from './inlineEditorComponents/FontWeight.vue';
import PanelLink from './inlineEditorComponents/PanelLink.vue';
import TextAlign from './inlineEditorComponents/TextAlign.vue';
import FontStyle from './inlineEditorComponents/FontStyles.vue';

// Stores
import { useUIStore } from '/@/editor/store';

const emit = defineEmits(['update:modelValue']);

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		forcedRootNode?: boolean | string;
	}>(),
	{
		modelValue: '',
		forcedRootNode: '',
	},
);

const TinyMCEEditor = {
	editor: null,
};

const UIStore = useUIStore();
const { modelValue } = toRefs(props);
const inlineEditorRef = ref(null);
const tooltipContentRef = ref(null);
const tinyMceReady = ref(false);
const showEditor = ref(false);
const isDragging = ref(false);
const dragButtonOnScreen = ref(true);
const initialPosition = ref({});
const lastPositionX = ref(0);
const lastPositionY = ref(0);
const yOffset = ref(0);
let windowObject = null;

const position = ref({
	offsetY: 75,
	offsetX: 0,
	posX: 0,
	posY: 0,
});

provide('ZionInlineEditor', TinyMCEEditor);

const barStyles = computed(() => {
	return {
		transform: `translate(${position.value.posX}px, ${position.value.posY}px)`,
	};
});

function saveContent() {
	emit('update:modelValue', TinyMCEEditor.editor.getContent());
}

function initWatcher() {
	watch(modelValue, (newValue, oldValue) => {
		if (
			TinyMCEEditor.editor &&
			typeof newValue === 'string' &&
			newValue !== oldValue &&
			newValue !== TinyMCEEditor.editor.getContent()
		) {
			TinyMCEEditor.editor.setContent(newValue);
		}
	});
}

function getConfig() {
	return {
		target: inlineEditorRef.value,
		entity_encoding: 'raw',
		toolbar: false,
		menubar: false,
		selection_toolbar: false,
		inline: true,
		object_resizing: false,
		setup: editor => {
			editor.on('init', () => {
				tinyMceReady.value = true;
				TinyMCEEditor.editor = editor;

				// Set the content
				editor.setContent(props.modelValue);

				// bind watchers
				initWatcher();
			});

			editor.on('change input undo redo', saveContent);
		},
		forced_root_block: '',
		formats: {
			fontSize: {
				selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
				classes: 'znpb-fontsize',
				styles: { fontSize: '%value' },
			},
			fontWeight: {
				inline: 'span',
				selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
				classes: 'znpb-fontweight',
				styles: { fontWeight: '%value' },
			},
			uppercase: {
				inline: 'span',
				selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
				styles: { textTransform: 'uppercase' },
			},
			blockquote: {
				block: 'blockquote',
				wrapper: true,
				classes: 'znpb-blockquote',
				exact: true,
			},
			italic: { inline: 'i', exact: true },
			lineHeight: {
				selector: 'span,p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
				styles: { 'line-height': '%value' },
			},
		},
	};
}

function onColorPickerOpen() {
	inlineEditorRef.value.classList.add('mce-content-body--selection-transparent');
}

function onColorPickerClose() {
	inlineEditorRef.value.classList.remove('mce-content-body--selection-transparent');
}

function startDrag(event: MouseEvent) {
	windowObject.addEventListener('mouseup', stopDrag);
	windowObject.addEventListener('mousemove', onDragMove);

	document.body.style.userSelect = 'none';

	initialPosition.value = {
		posX: event.clientX,
		posY: event.clientY,
	};

	isDragging.value = true;
	yOffset.value = windowObject.pageYOffset;
}

function onDragMove(event: MouseEvent) {
	position.value = {
		posX: lastPositionX.value + event.pageX - initialPosition.value.posX,
		posY: lastPositionY.value + event.pageY - initialPosition.value.posY - yOffset.value,
	};
}

function stopDrag() {
	// Save the last position
	lastPositionX.value = position.value.posX;
	lastPositionY.value = position.value.posY;

	// Check if the drag button is on the end of the drag action
	checkDragButtonOnScreen();

	// Unbind the listener that stops the drag since the drag stopper has been run
	document.body.style.removeProperty('user-select');
	windowObject.removeEventListener('mouseup', stopDrag);
	windowObject.removeEventListener('mousemove', onDragMove);

	isDragging.value = false;
}

function checkDragButtonOnScreen() {
	// Set the local data to the drag button position
	dragButtonOnScreen.value = !isDragButtonOutOfBounds();
}

function isDragButtonOutOfBounds() {
	// Gets the full position of the editor
	const inlineEditorPosition = getInlineEditorRect();

	// Check if the drag button is out of bounds on the left (X axis) side of the screen and return the response
	if (inlineEditorPosition) {
		return inlineEditorPosition.x < 40;
	}
}

function getInlineEditorRect() {
	// Get and send the inline editor rect for the position - beware, this does not include the drag button
	if (tooltipContentRef.value) {
		return tooltipContentRef.value.getBoundingClientRect();
	}
}

function hideEditorOnEscapeKey(event) {
	if (event.keyCode === 27) {
		hideEditor();
		event.stopImmediatePropagation();
	}
}

function hideEditor() {
	showEditor.value = false;
}

watch(showEditor, newValue => {
	if (newValue) {
		setTimeout(() => {
			document.addEventListener('keydown', hideEditorOnEscapeKey, true);
			document.addEventListener('scroll', hideEditor);
		}, 10);
	} else {
		document.removeEventListener('keydown', hideEditorOnEscapeKey, true);
		document.removeEventListener('scroll', hideEditor, true);
	}
});

function checkTextSelection(e: MouseEvent) {
	if (!e.view) {
		return;
	}

	const selection = e.view.getSelection();
	if (selection && selection.toString().length > 0) {
		showEditor.value = true;
	}
}

onMounted(() => {
	windowObject = inlineEditorRef.value.ownerDocument.defaultView;

	if (typeof windowObject.tinyMCE !== 'undefined') {
		windowObject.tinyMCE.init(getConfig());
	}
});

onBeforeUnmount(() => {
	// Destroy tinyMce
	if (typeof windowObject.tinyMCE !== 'undefined' && TinyMCEEditor.editor) {
		windowObject.tinyMCE.remove(TinyMCEEditor.editor);
	}
});
</script>

<style lang="scss">
.znpb-inline-editor__wrapper {
	border: none;
}

.znpb-inline-editor__wrapper_all {
	min-height: 14px;
}

.znpb-inline-text-editor--preview {
	.mce-visual-caret {
		display: none;
	}
}
.zion-inline-editor-container {
	display: flex;
	justify-content: space-around;
	align-items: center;
	padding: 2px;
	margin: 0 auto;
	color: var(--zb-surface-text-color);
	font-size: 14px;
	background: var(--zb-surface-color);
	box-shadow: 0 0 0 2px var(--zb-surface-border-color);
	border-radius: 3px;
	cursor: pointer;

	&__wrapper {
		display: flex;
		align-items: center;
	}
	& > .znpb-editor-icon-wrapper,
	& .zion-inline-editor-dragButton .znpb-editor-icon-wrapper {
		padding: 10px 11px;
	}
	.zion-inline-editor-dragButton {
		cursor: move;
	}
}

.znpb-inline-editor__wrapper {
	top: -75px;
	border: 0;

	button {
		padding: 0;
		margin: 0;
		background: transparent;
		border: none;
	}

	* {
		outline: none;
	}

	.znpb-editor-icon-wrapper {
		display: flex;
		margin: 0 1px;
		border-radius: 2px;
		transition: color 0.15s ease;

		&:hover {
			color: var(--zb-surface-text-hover-color);
		}

		.zion-icon {
			width: auto;
		}
	}

	.zion-inline-editor-button--active {
		color: var(--zb-secondary-text-color);
		background-color: var(--zb-secondary-color);
	}

	.zion-inline-editor-popover-wrapper--open {
		& > .znpb-editor-icon-wrapper {
			color: var(--zb-surface-text-color);
			background: var(--zb-surface-lighter-color);

			&:hover {
				color: var(--zb-surface-text-color);
				background: var(--zb-surface-lighter-color);
			}
		}
	}

	/** Buttons **/

	&--dragging {
		transition: none;
	}
}
.mce-content-body {
	line-height: inherit !important;
	outline: none;
	cursor: text;

	&--selection-transparent {
		& *::selection {
			background: transparent !important;
		}
	}
}

// NEW STYLES
.znpb-inline-text-editor {
	outline: none !important;
	cursor: text !important;

	& *::selection {
		background: rgba(133, 178, 232, 0.75);
	}
}

// fix for inline tinyMCE
.mce-tinyMCE-inline {
	border: none !important;
}
</style>
