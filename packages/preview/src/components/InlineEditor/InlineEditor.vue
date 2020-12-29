<template>
	<Tooltip
		tooltip-class="znpb-inline-editor__wrapper hg-popper--no-padding hg-popper--no-bg"
		:trigger="null"
		placement="top"
		append-to="body"
		:show-arrows="false"
		@hide="showEditor = false"
		:show="showEditor"
		strategy="fixed"
		ref="inlineEditorWrapper"
		@mousedown.stop=""
	>
		<template #content>
			<div
				v-if="!isPreviewMode && tinyMceReady"
				class="zion-inline-editor zion-inline-editor-container"
				:class="{'zion-inline-editor--dragging': isDragging}"
				:style="barStyles"
				ref="content"
				@mousedown.stop=""
				contenteditable="false"
			>
				<!--Normally positioned drag button-->
				<div
					class="zion-inline-editor-dragbutton"
					ref="dragButton"
					@mousedown.stop="startDrag"
					@mouseup="stopDrag"
					v-if="dragButtonOnScreen"
				>
					<Icon icon="ite-move" />
				</div>

				<!-- Fonts & text style panel -->
				<zion-inline-editor-panel
					icon="ite-font"
					:visible="activePanel==='fontsPanel'"
					@open-panel="activePanel='fontsPanel'"
				>
					<zion-inline-editor-group
						@started-dragging="onStartedSliderDragging"
						@units-expanded="onUnitsExpanded"
					/>

				</zion-inline-editor-panel>

				<!-- Bold popover -->
				<zion-inline-editor-popover
					icon="ite-weight"
					:full-width="true"
					:visible="activePanel==='boldPanel'"
					@open-panel="activePanel='boldPanel'"
				>
					<zion-inline-editor-font-weight
						v-for="fontWeight in fontWeights"
						:key="fontWeight"
						:modelValue="fontWeight"
					/>
				</zion-inline-editor-popover>

				<!-- Italic button -->
				<zion-inline-editor-button
					formatter="italic"
					icon="ite-italic"
				/>

				<!-- Underline button -->
				<zion-inline-editor-button
					formatter="underline"
					icon="ite-underline"
				/>
				<!-- Uppercase button -->
				<zion-inline-editor-button
					formatter="uppercase"
					icon="ite-uppercase"
				/>

				<!-- Link button -->

				<panelLink
					:full-width="true"
					direction="vertical"
				/>

				<!-- Quote button -->
				<zion-inline-editor-button
					formatter="blockquote"
					icon="ite-quote"
				/>

				<!-- Color Picker -->
				<zion-inline-editor-color-picker
					@close-color-picker="onColorPickerClose"
					@open-color-picker="onColorPickerOpen"
				/>

				<!-- Text align button -->
				<zion-inline-editor-popover
					icon="ite-alignment"
					:visible="activePanel==='alignPanel'"
					@open-panel="activePanel='alignPanel'"
				>

					<!-- Align left -->
					<zion-inline-editor-button
						@click="alignButtonIcon = 'align--left'"
						formatter="alignleft"
						icon="align--left"
					/>
					<!-- Align center -->
					<zion-inline-editor-button
						@click="alignButtonIcon = 'align--center'"
						formatter="aligncenter"
						icon="align--center"
					/>
					<!-- Align right -->
					<zion-inline-editor-button
						@click="alignButtonIcon = 'align--right'"
						formatter="alignright"
						icon="align--right"
					/>
					<!-- Align justify -->
					<zion-inline-editor-button
						@click="alignButtonIcon = 'align--justify'"
						formatter="alignjustify"
						icon="align--justify"
					/>

				</zion-inline-editor-popover>

				<!--Alternatively positioned drag button (if the normal one is out of bounds)-->
				<div
					class="zion-inline-editor-dragbutton"
					@mousedown.stop="startDrag"
					@mouseup="stopDrag"
					v-if="!dragButtonOnScreen"
				>
					<Icon
						icon="more"
						:rotate="90"
					/>

				</div>
			</div>
		</template>
		<div
			ref="inlineEditorRef"
			class="znpb-inline-text-editor"
			:class="{'znpb-inline-text-editor--preview': isPreviewMode}"
			@click="onMouseDown"
			@dblclick.stop="showEditor = true"
			:contenteditable="!isPreviewMode"
		></div>

	</Tooltip>

</template>

<script>
import { ref, computed, toRefs, onMounted, watch, onBeforeUnmount, provide } from "vue";

// Utils
import { usePreviewMode } from "@zb/editor";

// Components
import popOver from "./inlineEditorComponents/popOver.vue";
import panel from "./inlineEditorComponents/panel.vue";
import group from "./inlineEditorComponents/group.vue";
import buttonComponent from "./inlineEditorComponents/button.vue";
import colorPicker from "./inlineEditorComponents/colorPicker.vue";
import fontWeight from "./inlineEditorComponents/fontWeightButton.vue";
import panelLink from "./inlineEditorComponents/panelLink.vue";
import editorsManager from "./editorsManager";

export default {
	name: "InlineEditor",
	components: {
		"zion-inline-editor-popover": popOver,
		"zion-inline-editor-panel": panel,
		"zion-inline-editor-group": group,
		"zion-inline-editor-button": buttonComponent,
		"zion-inline-editor-color-picker": colorPicker,
		"zion-inline-editor-font-weight": fontWeight,
		panelLink,
	},
	props: {
		modelValue: {
			type: String,
			required: false,
		},
		forcedRootNode: {
			type: [Boolean, String],
			default: "p",
		},
	},
	setup (props, { emit }) {
		let TinyMCEEditor = ref(null);

		const fontWeights = [100, 200, 300, 400, 500, 600, 700, 800, 900]
		const { isPreviewMode } = usePreviewMode();
		const { modelValue } = toRefs(props);
		const inlineEditorRef = ref(null);
		const tinyMceReady = ref(false);
		const showEditor = ref(false);
		const isDragging = ref(false);
		const dragButtonOnScreen = ref(true);
		const activePanel = ref(null);
		const unitsExpanded = ref(false);
		const isSliderDragging = ref(false);


		const position = ref({
			offsetY: 75,
			offsetX: null,
			posX: null,
			posY: null
		})

		provide('ZionInlineEditor', TinyMCEEditor)

		const barStyles = computed(() => {
			return {
				transform: `translate(${position.value.posX}px, ${position.value.posY}px)`
			}
		})

		function saveContent () {
			emit("update:modelValue", TinyMCEEditor.value.getContent());
		}

		function initWatcher () {
			watch(modelValue, (newValue, oldValue) => {
				if (
					TinyMCEEditor.value &&
					typeof newValue === "string" &&
					newValue !== oldValue &&
					newValue !== TinyMCEEditor.value.getContent()
				) {
					TinyMCEEditor.value.setContent(newValue);
				}
			});
		}

		function getConfig () {
			// let self = this
			return {
				target: inlineEditorRef.value,
				entity_encoding: "raw",
				toolbar: false,
				menubar: false,
				selection_toolbar: false,
				inline: true,
				object_resizing: false,
				setup: (editor) => {
					editor.on("init", (e) => {
						tinyMceReady.value = true;
						TinyMCEEditor.value = editor;

						// Set the content
						editor.setContent(props.modelValue);

						// bind watchers
						initWatcher();
					});

					editor.on("change input undo redo", saveContent);
				},
				forced_root_block: props.forcedRootNode,
				formats: {
					fontSize: {
						inline: "span",
						classes: "znpb-fontsize",
						styles: { fontSize: "%value" },
					},
					fontweight: {
						inline: "span",
						classes: "znpb-fontweight",
						styles: { fontWeight: "%value" },
					},
					uppercase: {
						inline: "span",
						classes: "znpb-uppercase",
						styles: { textTransform: "uppercase" },
					},
					blockquote: {
						block: "blockquote",
						wrapper: true,
						classes: "znpb-blockquote",
						exact: true,
					},
					italic: { inline: "i", exact: true },
				},
			};
		}

		function onColorPickerOpen () {
			activePanel.value = 'colorPicker'
			inlineEditorRef.value.classList.add('mce-content-body--selection-transparent')
		}

		function onColorPickerClose () {
			inlineEditorRef.value.classList.remove('mce-content-body--selection-transparent')
		}

		function onUnitsExpanded (event) {
			unitsExpanded.value = event
		}

		function onStartedSliderDragging () {
			document.addEventListener('mouseup', onDraggingInput)
			isSliderDragging.value = true
		}

		function onDraggingInput () {
			setTimeout(() => {
				isSliderDragging.value = false
			}, 300)
		}

		function onOutsideClick (event) {
			// TODO: add this
			// if (this.canClose && this.$refs.content && !this.$refs.content.contains(event.target) && !this.$refs.inlineEditor.contains(event.target) && !this.isSliderDragging && !this.unitsExpanded) {
			// 	this.isInlineEditorVisible = false
			// }
		}

		onMounted(() => {
			if (typeof window.tinyMCE !== "undefined") {
				window.tinyMCE.init(getConfig());
			}
		});

		onBeforeUnmount(() => {
			// Destroy tinyMce
			if (typeof window.tinyMCE !== 'undefined' && TinyMCEEditor.value) {
				window.tinyMCE.remove(TinyMCEEditor.value)
			}
		})

		return {
			isPreviewMode,
			inlineEditorRef,
			showEditor,
			tinyMceReady,
			isDragging,
			dragButtonOnScreen,
			activePanel,
			barStyles,
			fontWeights,
			// Methods
			onColorPickerOpen,
			onColorPickerClose,
			onUnitsExpanded,
			onStartedSliderDragging,

		};
	},
};
</script>

<style lang="scss">
.znpb-inline-editor__wrapper {
	pointer-events: none;
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
	color: $font-color;
	font-size: 14px;
	background: $surface;
	box-shadow: 0 0 0 2px $border-color;
	border-radius: 3px;
	cursor: pointer;

	&__wrapper {
		display: flex;
		align-items: center;
	}
	& > .znpb-editor-icon-wrapper, & .zion-inline-editor-dragbutton .znpb-editor-icon-wrapper {
		padding: 10px 11px;
	}
	.zion-inline-editor-dragbutton {
		cursor: move;
	}
}

.zion-inline-editor-dropdown {
	.zion-input input::placeholder {
		color: $surface-headings-color;
	}
}

.zion-inline-editor {
	top: -75px;
	transition: all .2s;
	pointer-events: all;
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
		transition: color .15s ease;

		&:not(.znpb-input-number__dots):hover {
			color: darken($font-color, 10%);
			background: $surface-variant;
		}

		.zion-icon {
			width: auto;
		}

		&.zion-inline-editor-button--active {
			color: $surface;
			background-color: $secondary;

			&:hover {
				color: $surface;
				background-color: $secondary;
			}
		}
	}

	.zion-inline-editor-popover-wrapper--open {
		& > .znpb-editor-icon-wrapper {
			color: darken($font-color, 10%);
			background: $surface-variant;

			&:hover {
				color: darken($font-color, 10%);
				background: $surface-variant;
			}
		}
	}

	/** Buttons **/

	&--dragging {
		transition: none;
	}
}

/* Bar animations */
.barShow-enter-to, .barShow-leave-from {
	transition: all .2s;
}
.barShow-enter-from, .barShow-leave-to {
	opacity: 0;
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
		background: rgba(133, 178, 232, .75);
	}
}

// fix for inline tinymce
.mce-tinymce-inline {
	border: none !important;
}
</style>
