<template>
	<Tooltip
		tooltip-class="znpb-inline-editor__wrapper hg-popper--no-padding hg-popper--no-bg"
		:trigger="null"
		placement="top"
		append-to="body"
		:show-arrows="false"
		:show="canShowEditor"
		:modifiers="{
			flip: {
				behavior: ['left', 'bottom', 'top']
			},
			offset: { offset: '0,0' }
		}"
		@hide="hideInlineEditor"
		strategy="fixed"
		ref="inlineEditorWrapper"
		@mousedown.stop=""
	>
		<template #content>
			<div
				v-if="!isPreviewMode"
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
						@active-font="activeFont=$event"
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
					:visible="activePanel==='linkPanel'"
					@open-panel="activePanel='linkPanel'"
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
			v-if="editor"
			ref="inlineEditor"
			class="znpb-inline-text-editor"
			:class="{'znpb-inline-text-editor--preview': isPreviewMode}"
			@click="onMouseDown"
			@dblclick.stop=""
			:contenteditable="!isPreviewMode"
		></div>
		<div
			v-else
			@click="onMouseDown"
			:contenteditable="!isPreviewMode"
			@dblclick.stop=""
			:class="{'znpb-inline-text-editor--preview': isPreviewMode}"
			class="znpb-inline-text-editor"
			ref="inlineEditor"
			v-html="modelValue"
		>
		</div>
	</Tooltip>

</template>

<script>
// Utils
import { mapGetters, mapActions } from 'vuex'

// Components
import popOver from './inlineEditorComponents/popOver.vue'
import panel from './inlineEditorComponents/panel.vue'
import group from './inlineEditorComponents/group.vue'
import buttonComponent from './inlineEditorComponents/button.vue'
import colorPicker from './inlineEditorComponents/colorPicker.vue'
import fontWeight from './inlineEditorComponents/fontWeightButton.vue'
import panelLink from './inlineEditorComponents/panelLink.vue'
import editorsManager from './editorsManager'

export default {
	name: 'InlineEditor',
	provide () {
		return {
			Editor: this
		}
	},

	components: {
		'zion-inline-editor-popover': popOver,
		'zion-inline-editor-panel': panel,
		'zion-inline-editor-group': group,
		'zion-inline-editor-button': buttonComponent,
		'zion-inline-editor-color-picker': colorPicker,
		'zion-inline-editor-font-weight': fontWeight,
		panelLink
	},

	props: {
		modelValue: {
			type: String,
			required: false
		},
		forcedRootNode: {
			type: [Boolean, String],
			default: 'p'
		}
	},
	data () {
		return {
			localContent: '',
			canClose: true,
			activeFont: null,
			editor: null,
			tinyMceReady: false,
			activePanel: null,
			getContent: null,
			yOffset: null,
			/// OLD VALUES

			// Visibility Variables
			isInlineEditorVisible: false,

			// TinyMCE Components
			activeEditor: null,
			activeElement: null,
			activeEditorOptionId: null,

			// Font weights
			fontWeights: [100, 200, 300, 400, 500, 600, 700, 800, 900],

			// Inline Editor position on page
			position: {
				offsetY: 75,
				offsetX: null,
				posX: null,
				posY: null
			},
			lastPositionX: 0,
			lastPositionY: 0,
			isDragging: false,

			mouseToContentLeft: 0,
			mouseToContentTop: 0,
			// Out of bounds variables
			dragButtonOnScreen: true, // False if the editor drag button is out of the browser window
			initialPosition: null,
			isSliderDragging: false,
			alignButtonIcon: 'align--center',
			unitsExpanded: null
		}
	},

	computed: {
		...mapGetters([
			'isPreviewMode'
		]),
		uid () {
			return this.$parent.data.uid
		},
		data () {
			return this.$parent.data
		},
		editorID () {
			return `znpbwpeditor${this._uid}`
		},
		canShowEditor () {
			return this.isInlineEditorVisible && this.tinyMceReady && !this.isPreviewMode
		},
		content: {
			get () {
				return this.modelValue
			},
			set (newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},
		// This is the initial bar position setter (through a style element)
		barStyles: function () {
			const styles = {
				transform: `translate(${this.position.posX}px, ${this.position.posY}px)`
			}
			return styles
		},

		// Check if the bar is loaded on screen
		isActiveBar: function () {
			return this.isInlineEditorVisible
		}
	},
	created () {
		this.localContent = this.modelValue
	},
	methods: {
		...mapActions([
			'updateElementOptionValue'
		]),
		onColorPickerOpen () {
			this.activePanel = 'colorPicker'
			const editor = this.$refs.inlineEditor
			editor.classList.add('mce-content-body--selection-transparent')
		},
		onColorPickerClose () {
			this.activePanel = 'colorPicker'
			const editor = this.$refs.inlineEditor
			editor.classList.remove('mce-content-body--selection-transparent')
		},
		onUnitsExpanded (event) {
			this.unitsExpanded = event
		},
		onStartedSliderDragging () {
			document.addEventListener('mouseup', this.onDraggingInput)
			this.isSliderDragging = true
		},
		onDraggingInput () {
			setTimeout(() => {
				this.isSliderDragging = false
			}, 300)
		},
		onOutsideClick (event) {
			if (this.canClose && this.$refs.content && !this.$refs.content.contains(event.target) && !this.$refs.inlineEditor.contains(event.target) && !this.isSliderDragging && !this.unitsExpanded) {
				this.isInlineEditorVisible = false
			}
		},
		onMouseDown () {
			if (!this.isInlineEditorVisible && !this.isPreviewMode) {
				this.showEditor()
			}
		},
		preventClose () {
			this.canClose = false
		},
		allowClose () {
			this.canClose = true
		},
		resetPosition () {
			if (this.position && this.initialPosition) {
				setTimeout(() => {
					this.position.posX = 0
					this.position.posY = 0
					this.initialPosition.posX = 0
					this.initialPosition.posY = 0
					this.lastPositionX = 0
					this.lastPositionY = 0
					this.yOffset = 0
				}, 300)
			}
		},
		hideInlineEditor () {
			this.activePanel = null
			this.isInlineEditorVisible = false
		},
		hideEditor () {
			this.isInlineEditorVisible = false
		},
		showEditor () {
			if (!this.isPreviewMode && !this.isInlineEditorVisible) {
				this.isInlineEditorVisible = true
			}
		},

		startDrag (event) {
			window.addEventListener('mouseup', this.stopDrag)
			window.addEventListener('mousemove', this.onDragMove)

			document.body.style.userSelect = 'none'

			this.initialPosition = {
				posX: event.clientX,
				posY: event.clientY
			}

			this.isDragging = true
			this.yOffset = window.pageYOffset
		},
		onDragMove (event) {
			this.position = {
				posX: this.lastPositionX + event.pageX - this.initialPosition.posX,
				posY: this.lastPositionY + event.pageY - this.initialPosition.posY - this.yOffset
			}
		},
		stopDrag (event) {
			// Save the last position
			this.lastPositionX = this.position.posX
			this.lastPositionY = this.position.posY

			// Check if the drag button is on the end of the drag action
			this.checkDragButtonOnScreen()

			// Unbind the listener that stops the drag since the drag stopper has been run
			document.body.style.userSelect = null
			window.removeEventListener('mouseup', this.stopDrag)
			window.removeEventListener('mousemove', this.onDragMove)
			this.isDragging = false
		},
		getInlineEditorRect () {
			// Get and send the inline editor rect for the position - beware, this does not include the drag button
			if (this.$refs.content) {
				return this.$refs.content.getBoundingClientRect()
			}
		},
		getContentRect () {
			if (this.$refs.content) {
				return this.$refs.content.getBoundingClientRect()
			}
		},
		getPositionByMouseLocation (mouseLocationX) {
			// Get the inline editor rect for the scale - beware, this does not include the drag button
			let inlineEditorRect = this.getInlineEditorRect()

			// Send back the space between the left side (x) of the screen and the start of the inline editor WITHOUT the drag button
			return { x: (mouseLocationX - (inlineEditorRect.width / 2)) }
		},

		isDragButtonOutOfBounds () {
			// Gets the full position of the editor
			let inlineEditorPosition = this.getInlineEditorRect()

			// Check if the drag button is out of bounds on the left (X axis) side of the screen and return the response
			if (inlineEditorPosition) {
				return inlineEditorPosition.x < 40
			}
		},

		checkDragButtonOnScreen () {
			// Set the local data to the drag button position
			this.dragButtonOnScreen = !this.isDragButtonOutOfBounds()
		},

		checkDragButtonOnScreenByMouseLocation (mouseLocationX) {
			// Gets the full position of the editor
			let inlineEditorPosition = this.getPositionByMouseLocation(mouseLocationX)

			// Check if the drag button is in bounds on the left (X axis) side of the screen and return the response
			if (inlineEditorPosition) {
				this.dragButtonOnScreen = inlineEditorPosition.x > 40
			}
		},
		saveContent () {
			if (this.editor) {
				this.content = this.editor.getContent()
				this.currentContent = this.content
			}
		},
		onTiyMceSetup (editor) {
			editor.onKeyDown.add(function (editor, e) {
				if (e.keyCode === 9) {
					editor.execCommand('mceInsertContent', false, '&emsp;')
					e.preventDefault()
				}
			})
			editor.on('init', () => {
				this.tinyMceReady = true

				// Set editor content
				this.editor.setContent(this.content)
				this.currentContent = this.content

				this.showEditor()
			})

			this.editor = editor
			editor.on('change KeyUp Undo Redo', this.saveContent)
		},
		initTinyMCE () {
			if (this.editor) {
				return
			}

			// let self = this
			let config = {
				target: this.$refs.inlineEditor,
				entity_encoding: 'raw',
				toolbar: false,
				menubar: false,
				selection_toolbar: false,
				inline: true,
				object_resizing: false,
				setup: this.onTiyMceSetup,
				forced_root_block: this.forcedRootNode,
				formats: {
					fontSize: { inline: 'span', classes: 'znpb-fontsize', styles: { fontSize: '%value' } },
					fontweight: { inline: 'span', classes: 'znpb-fontweight', styles: { fontWeight: '%value' } },
					uppercase: { inline: 'span', classes: 'znpb-uppercase', styles: { textTransform: 'uppercase' } },
					blockquote: { block: 'blockquote', wrapper: true, classes: 'znpb-blockquote', exact: true },
					italic: { inline: 'i', exact: true }
				}
			}
			if (typeof window.tinyMCE !== 'undefined') {
				window.tinyMCE.init(config)
			}
		},
		hideEditorOnEscapeKey (event) {
			if (event.keyCode === 27) {
				this.isInlineEditorVisible = false
				event.stopImmediatePropagation()
			}
		}
	},

	watch: {
		content (newValue, oldValue) {
			if (!this.editor) {
				this.currentContent = newValue
				return
			}
			const currentValue = this.editor.getContent()
			if (currentValue !== newValue && this.currentContent !== newValue) {
				this.editor.setContent(newValue)
				this.currentContent = newValue
			}
		},
		canShowEditor (newValue, oldValue) {
			if (newValue) {
				// hide on escape
				document.addEventListener('scroll', this.hideInlineEditor)
				document.addEventListener('click', this.onOutsideClick, true)
				document.addEventListener('keydown', this.hideEditorOnEscapeKey, true)
			} else {
				document.removeEventListener('click', this.onOutsideClick, true)
				document.removeEventListener('keydown', this.hideEditorOnEscapeKey, true)
				document.removeEventListener('scroll', this.hideInlineEditor)
			}
		},
		isInlineEditorVisible (newValue) {
			if (newValue) {
				this.initTinyMCE()
				editorsManager.opendEditor(this)
			} else {
				editorsManager.closeEditor()
				// Destroy tinyMce
				if (typeof window.tinyMCE !== 'undefined') {
					window.tinyMCE.remove(this.editor)
				}
				this.resetPosition()

				this.$nextTick(() => {
					this.editor = null
				})
			}
		}
	},

	beforeUnmount () {
		editorsManager.closeEditor()
		document.removeEventListener('keydown', this.hideEditorOnEscapeKey, true)
		document.removeEventListener('click', this.onOutsideClick, true)
		document.removeEventListener('scroll', this.hideInlineEditor)

		// Destroy tinyMce
		if (typeof window.tinyMCE !== 'undefined') {
			window.tinyMCE.remove(this.editor)
		}
	}

}

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
			background: transparent;
		}
	}
}

// NEW STYLES
.znpb-inline-text-editor {
	cursor: text !important;
	&:focus {
		outline: none;
	}
}

// fix for inline tinymce
.mce-tinymce-inline {
	border: none !important;
}
</style>
