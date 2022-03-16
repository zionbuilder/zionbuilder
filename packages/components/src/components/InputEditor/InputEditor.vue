<template>
	<div
		class="znpb-wp-editor__wrapper znpb-wp-editor-custom"
		ref="root"
	></div>
</template>

<script>
import { ref, onBeforeUnmount, onMounted, computed, watch } from 'vue'
import { debounce } from '@zb/utils'

export default {
	name: 'InputEditor',
	props: {
		modelValue: {
			type: String,
			required: false
		}
	},
	setup (props, { emit }) {
		let editorTextarea = null
		const root = ref(null)
		let editor = null
		const randomNumber = Math.floor((Math.random() * 100) + 1);
		const editorID = `znpbwpeditor${randomNumber}`;

		// computed
		const content = computed({
			get () {
				return props.modelValue || ''
			},
			set (newValue) {
				emit('update:modelValue', newValue)
			}
		})

		// Lifecycle
		onBeforeUnmount(() => {
			editorTextarea.removeEventListener('keyup', onTextChanged)

			// Destroy tinyMce
			if (typeof window.tinyMCE !== 'undefined' && editor) {
				window.tinyMCE.remove(editor)
			}

			editor = null
		})

		onMounted(() => {
			root.value.innerHTML = window.ZnPbInitalData.wp_editor.replace(/znpbwpeditorid/g, editorID).replace('%%ZNPB_EDITOR_CONTENT%%', content.value)

			editorTextarea = document.querySelectorAll('.wp-editor-area')[0]
			editorTextarea.addEventListener('keyup', onTextChanged)

			window.quicktags({
				buttons: 'strong,em,del,link,img,close',
				id: editorID
			})

			const config = {
				id: editorID,
				selector: `#${editorID}`,
				setup: onEditorSetup,
				content_style: "body { background-color: #fff; }"
			}
			window.tinyMCEPreInit.mceInit[editorID] = Object.assign({}, window.tinyMCEPreInit.mceInit.znpbwpeditorid, config)

			// Set the edit mode to tmce
			window.switchEditors.go(editorID, 'tmce')
		})

		// Watchers
		watch(() => props.modelValue, (newValue, oldValue) => {
			const currentValue = editor.getContent()
			if (editor && currentValue !== newValue) {
				const value = newValue || ''
				editor.setContent(value)
				debouncedAddToHistory()
				editorTextarea.value = newValue
			}
		})

		const debouncedAddToHistory = debounce(() => {
			if (editor) {
				editor.undoManager.add()
			}
		}, 500)

		function onEditorSetup (editorInstance) {
			editor = editorInstance
			editor.on('change KeyUp Undo Redo', onEditorContentChange)
		}

		function onEditorContentChange (event) {
			const currentValue = props.modelValue

			const newValue = editor.getContent()

			if (currentValue !== newValue) {
				emit('update:modelValue', newValue)
			}
		}

		function onTextChanged (event) {
			emit('update:modelValue', editorTextarea.value)
		}

		return {
			root,
			editor,
			editorID,
			debouncedAddToHistory
		}
	},
	mounted () {

	},
	watch: {

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
		flex-shrink: 0;
		margin-left: auto;
	}

	button {
		display: inline-flex;
		align-items: center;
		color: var(--zb-surface-text-active-color);
		font-family: Roboto, sans-serif;
		font-size: 13px;
		font-weight: 500;
		line-height: 1.5;
	}

	.button {
		color: var(--zb-surface-text-active-color);
		font-size: 13px;
		font-weight: 500;
		line-height: 1.5;
		background-color: transparent;
		border: none;
		transition: opacity .15s;

		&:hover, &:focus {
			color: var(--zb-surface-text-active-color);
			background: transparent;
		}

		&:focus {
			box-shadow: none;
		}

		&.insert-media {
			min-height: 34px;
			padding: 7.5px 16px;
			margin-bottom: 10px;
			color: var(--zb-secondary-text-color);
			background: var(--zb-secondary-color);
			border-radius: 3px;
			transition: background-color .15s;

			&:hover {
				background: var(--zb-secondary-hover-color);
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

	.wp-media-buttons .edd-thickbox {
		padding: 10px 0 !important;
		margin: 0;
		color: var(--zb-surface-text-color);
		font-size: 10px;
	}

	.wp-switch-editor {
		top: 0;
		display: inline-flex;
		align-items: center;
		box-sizing: border-box;
		height: 30px;
		padding: 6px 12px;
		margin: 0;
		color: var(--zb-surface-text-color);
		background: transparent;
		border: none;
	}

	.switch-tmce, .switch-html {
		&:focus, &:active {
			color: var(--zb-surface-text-color);
			background: var(--zb-surface-color);
		}
	}

	.tmce-active .switch-tmce, .html-active .switch-html {
		color: var(--zb-surface-text-active-color);
		background: var(--zb-surface-lighter-color);
		border-top-right-radius: 3px;
		border-top-left-radius: 3px;
	}

	.mce-panel.mce-menu {
		color: var(--zb-dropdown-text-color);
		background: var(--zb-dropdown-bg-color);
		box-shadow: var(--zb-dropdown-shadow);
		border: 1px solid var(--zb-dropdown-border-color);
		border-radius: 3px;
	}

	.mce-menu .mce-menu-item.mce-active.mce-menu-item-normal, .mce-menu .mce-menu-item.mce-active.mce-menu-item-preview, .mce-menu .mce-menu-item.mce-selected, .mce-menu .mce-menu-item:focus, .mce-menu .mce-menu-item:hover {
		color: var(--zb-dropdown-text-active-color);
		background: var(--zb-dropdown-bg-hover-color);
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
		background: var(--zb-surface-lighter-color);
		border-bottom: 2px solid var(--zb-surface-border-color);
	}

	.quicktags-toolbar {
		background: var(--zb-surface-lighter-color);
		border-bottom: 2px solid var(--zb-surface-border-color);
	}

	.mce-toolbar .mce-btn-group > div {
		display: flex;
		flex-wrap: wrap;
	}

	.mce-toolbar .mce-btn.mce-active .mce-open, .mce-toolbar .mce-btn:focus .mce-open, .mce-toolbar .mce-btn:hover .mce-open {
		border-left-color: var(--zb-surface-border-color);
	}

	.mce-toolbar .mce-ico {
		color: var(--zb-surface-text-color);
	}

	.mce-btn .mce-caret {
		margin-top: 2px;
	}

	.mce-toolbar .mce-btn-group .mce-btn, .qt-dfw {
		border: 0;
	}

	.mce-toolbar .mce-btn-group .mce-btn.mce-listbox {
		color: var(--zb-input-text-color);
		background: var(--zb-input-bg-color);
		border: 2px solid var(--zb-surface-border-color);
		border-radius: 3px;

		&:focus, &:hover {
			border-color: var(--zb-surface-border-color);
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
			border: 2px solid var(--zb-surface-border-color);
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
		background: var(--zb-surface-lightest-color);
		box-shadow: none;
		border: 0;
	}

	.mce-toolbar .mce-btn-group .mce-btn:not(.mce-listbox).mce-active {
		color: var(--zb-surface-color);
		background: var(--zb-secondary-color);
		box-shadow: none;
		border: 0;
	}

	.mce-btn button {
		color: var(--zb-surface-text-color);
	}

	.mce-toolbar .mce-btn button, .qt-dfw {
		height: 100%;
	}

	div.mce-panel {
		background: var(--zb-surface-lighter-color);
	}

	.mce-edit-area {
		color: var(--zb-input-text-color);
		background: var(--zb-input-bg-color);
		border: var(--zb-input-border-color);
	}

	div.mce-statusbar {
		border-color: var(--zb-surface-border-color);
		border-style: solid;
		border-width: 1px 2px 2px !important;
	}

	div.mce-path {
		padding: 6px 10px;
	}

	textarea.wp-editor-area {
		border-color: var(--zb-surface-border-color);
		border-style: solid;
		border-width: 0 2px 2px;
	}

	.quicktags-toolbar input.ed_button {
		min-height: 20px;
		padding: 4px 10px;
		line-height: 1.5;
		border: none;

		&:hover {
			background: var(--zb-surface-light-color);
		}
	}
}
</style>
