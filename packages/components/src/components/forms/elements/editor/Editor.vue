<template>
	<div class="znpb-wp-editor__wrapper znpb-wp-editor-custom"></div>
</template>

<script>
export default {
	name: 'Editor',
	props: {
		modelValue: {
			type: String,
			required: false
		}
	},
	data () {
		return {
			editor: null,
			editorTextarea: null
		}
	},
	computed: {
		content: {
			get () {
				return this.modelValue || ''
			},
			set (newValue, oldValue) {
				if (newValue !== oldValue) {

				}
				this.$emit('update:modelValue', newValue)
			}
		},
		editorID () {
			return `znpbwpeditor${this._uid}`
		}
	},
	mounted () {
		this.$el.innerHTML = window.ZnPbInitalData.wp_editor.replace(/znpbwpeditorid/g, this.editorID).replace('%%ZNPB_EDITOR_CONTENT%%', this.content)

		this.editorTextarea = document.querySelectorAll('.wp-editor-area')[0]

		this.editorTextarea.addEventListener('keyup', this.onTextChanged)

		window.quicktags({
			buttons: 'strong,em,del,link,img,close',
			id: this.editorID
		})

		const config = {
			id: this.editorID,
			selector: `#${this.editorID}`,
			setup: this.onEditorSetup
		}
		window.tinyMCEPreInit.mceInit[ this.editorID ] = Object.assign({}, window.tinyMCEPreInit.mceInit.znpbwpeditorid, config)

		// Set the edit mode to tmce
		window.switchEditors.go(this.editorID, 'tmce')
	},
	methods: {
		onEditorSetup (editor) {
			this.editor = editor
			editor.on('change KeyUp Undo Redo', this.onEditorContentChange)
		},
		onEditorContentChange (event) {
			const currentValue = this.modelValue

			const newValue = this.editor.getContent()

			if (currentValue !== newValue) {
				this.$emit('update:modelValue', newValue)
			}
		},
		onTextChanged (event) {
			this.$emit('update:modelValue', this.editorTextarea.value)
		}
	},
	watch: {
		value (newValue, oldValue) {
			const currentValue = this.editor.getContent()
			if (this.editor && currentValue !== newValue) {
				const value = newValue || ''
				this.editor.setContent(value)
			}
		}
	}
}
</script>

<style lang="scss">
.wp-editor-wrap {
	z-index: 0;
}

.znpb-wp-editor-custom {
	.wp-editor-tools {
		display: flex;
		justify-content: space-between;
		align-items: flex-end;
	}

	.wp-editor-tabs, .wp-media-buttons {
		float: none;
	}

	.wp-editor-tabs {
		margin-left: auto;
	}

	button {
		display: inline-flex;
		align-items: center;
		color: $surface-active-color;
		font-family: Roboto, sans-serif;
		font-size: 13px;
		font-weight: 500;
		line-height: 1.5;
	}

	.button {
		color: $surface-active-color;
		font-size: 13px;
		font-weight: 500;
		line-height: 1.5;
		background-color: transparent;
		border: none;
		transition: opacity .15s;

		&:hover, &:focus {
			color: $surface-active-color;
			background: transparent;
		}

		&:focus {
			box-shadow: none;
		}

		&.insert-media {
			min-height: 34px;
			padding: 7.5px 16px;
			margin-bottom: 10px;
			color: $surface;
			background: $secondary;
			border-radius: 3px;
			transition: background-color .15s;

			&:hover {
				background: darken($secondary, 5%);
			}

			&:active {
				margin-bottom: 10px !important;
			}

			.wp-media-buttons-icon {
				display: none;
			}
		}
	}

	.wp-media-buttons .button:active {
		top: 0;
		margin: 0 5px 4px 0;
	}

	.wp-switch-editor {
		top: 0;
		display: inline-flex;
		align-items: center;
		box-sizing: border-box;
		height: 30px;
		padding: 6px 12px;
		margin: 0;
		color: $font-color;
		background: transparent;
		border: none;
	}

	.tmce-active .switch-tmce, .html-active .switch-html {
		background: $surface-variant;
		border-top-right-radius: 3px;
		border-top-left-radius: 3px;
	}

	.wp-editor-container {
		border: 0;
		border-radius: 3px;
	}

	div.mce-toolbar-grp > div, .quicktags-toolbar {
		padding: 10px;
	}

	.mce-top-part::before {
		display: none;
	}

	div.mce-toolbar-grp {
		background: $surface-variant;
		border-bottom: 2px solid $border-color;
	}

	.quicktags-toolbar {
		background: $surface-variant;
		border-bottom: 2px solid $border-color;
	}

	.mce-toolbar .mce-btn-group > div {
		display: flex;
		flex-wrap: wrap;
	}

	.mce-toolbar .mce-btn.mce-active .mce-open, .mce-toolbar .mce-btn:focus .mce-open, .mce-toolbar .mce-btn:hover .mce-open {
		border-left-color: #d0d0d0;
	}

	.mce-btn .mce-caret {
		margin-top: 2px;
	}

	.mce-toolbar .mce-btn-group .mce-btn, .qt-dfw {
		border: 0;
	}

	.mce-toolbar .mce-btn-group .mce-btn.mce-listbox {
		border: 2px solid $border-color;
		border-radius: 3px;

		&:focus, &:hover {
			border-color: $border-color;
		}

		.mce-txt {
			font-weight: 500;
		}

		.mce-caret {
			right: 10px;
			box-sizing: border-box;
			width: 6px;
			height: 6px;
			margin-top: -4px;
			border: 2px solid #bcbcbc;
			border-top: none;
			border-left: none;
			transform: rotate(45deg);
		}
	}

	.mce-btn {
		box-shadow: none;
		border: 0;
	}

	.mce-toolbar .mce-btn-group .mce-btn:not(.mce-listbox):focus, .mce-toolbar .mce-btn-group .mce-btn:not(.mce-listbox):hover, .qt-dfw:focus, .qt-dfw:hover, .mce-toolbar .mce-btn-group .mce-btn:not(.mce-listbox):active, .qt-dfw.active {
		background: darken($surface-variant, 5%);
		box-shadow: none;
		border: 0;
	}

	.mce-toolbar .mce-btn-group .mce-btn:not(.mce-listbox).mce-active {
		color: $surface;
		background: $secondary;
		box-shadow: none;
		border: 0;
	}

	.mce-toolbar .mce-btn button, .qt-dfw {
		height: 100%;
	}

	.mce-edit-area {
		border-color: $border-color;
		border-style: solid;
		border-width: 0 2px 0 !important;
	}

	div.mce-statusbar {
		border-color: $border-color;
		border-style: solid;
		border-width: 1px 2px 2px !important;
	}

	div.mce-path {
		padding: 6px 10px;
	}

	textarea.wp-editor-area {
		border-color: $border-color;
		border-style: solid;
		border-width: 0 2px 2px;
	}

	.quicktags-toolbar input.ed_button {
		min-height: 20px;
		padding: 4px 10px;
		line-height: 1.5;
		border: none;

		&:hover {
			background: darken($surface-variant, 5%);
		}
	}
}
</style>
