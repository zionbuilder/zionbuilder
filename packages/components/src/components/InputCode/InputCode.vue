<template>
	<div class="znpb-custom-code">
		<textarea
			class="znpb-custom-code__text-area"
			ref="codeMirrorTextarea"
			v-model="modelValue"
			:placeholder="placeholder"
		></textarea>
	</div>
</template>

<script>
import { ref, watch, onMounted } from 'vue'

export default {
	name: 'InputCode',
	props: {
		placeholder: {
			type: String
		},
		mode: {
			type: String
		},
		modelValue: {
			type: String,
			required: false,
			default () {
				return ''
			}
		}
	},
	setup (props, { emit }) {
		let editor = null
		const codeMirrorTextarea = ref(null)

		watch(() => props.modelValue, (newValue) => {
			if (editor && editor.getValue() !== newValue) {
				editor.setValue(newValue)
			}
		})

		function onEditorChange (instance, changeObj) {
			emit('update:modelValue', instance.getValue())
		}

		const lint = [
			'text/css',
			'text/x-scss',
			'text/x-less',
			'text/javascript',
			'application/json',
			'application/ld+json',
			'text/typescript',
			'application/typescript',
			'htmlmixed',
		].includes(props.mode) ? true : false

		onMounted(() => {
			editor = window.wp.CodeMirror.fromTextArea(codeMirrorTextarea.value, {
				mode: props.mode,
				lineNumbers: true,
				lineWrapping: true,
				lint,
				autoCloseBrackets: true,
				matchBrackets: true,
				autoCloseTags: true,
				matchTags: {
					bothTags: true
				},
				csslint: {
					errors: true,
					'box-model': true,
					'display-property-grouping': true,
					'duplicate-properties': true,
					'known-properties': true,
					'outline-none': true
				},
				jshint: {
					'boss': true,
					'curly': true,
					'eqeqeq': true,
					'eqnull': true,
					'es3': true,
					'expr': true,
					'immed': true,
					'noarg': true,
					'nonbsp': true,
					'onevar': true,
					'quotmark': 'single',
					'trailing': true,
					'undef': true,
					'unused': true,
					'browser': true,
					'globals': {
						'_': false,
						'Backbone': false,
						'jQuery': false,
						'JSON': false,
						'wp': false
					}
				},
				htmlhint: {
					'tagname-lowercase': true,
					'attr-lowercase': true,
					'attr-value-double-quotes': false,
					'doctype-first': false,
					'tag-pair': true,
					'spec-char-escape': true,
					'id-unique': true,
					'src-not-empty': true,
					'attr-no-duplication': true,
					'alt-require': true,
					'space-tab-mixed-disabled': 'tab',
					'attr-unsafe-chars': true
				},
				// Show lint error numbers
				gutters: ['CodeMirror-lint-markers']
			})

			// Set the editor value
			if (props.modelValue) {
				// editor.setValue(this.modelValue)
			}

			// Bind events
			editor.on('change', onEditorChange)
		})

		return {
			codeMirrorTextarea
		}
	}
}
</script>

<style lang="scss">
.znpb-custom-code {
	&__text-area {
		width: 100%;
	}

	.CodeMirror {
		position: relative;
		overflow: hidden;
		color: #768390;
		background: var(--zb-surface-darker-color);

		pre {
			color: #768390;
			line-height: 1.4;
		}

		&-gutters {
			background-color: var(--zb-surface-lighter-color);
			border-right: 1px solid var(--zb-surface-border-color);
		}

		&-linenumber {
			color: #768390;
		}
	}
}
.znpb-custom-code * {
	box-sizing: content-box !important;
}

.znpb-theme-dark .znpb-custom-code {
	.CodeMirror-selected {
		background: #253549;
	}

	.CodeMirror-focused .CodeMirror-selected {
		background: #274467;
	}

	div.CodeMirror span.CodeMirror-matchingbracket {
		color: #b9b9b9;
	}

	.CodeMirror-cursor {
		border-color: #2c89df;
	}

	.cm-atom,
	.cm-keyword,
	.cm-builtin,
	.cm-meta,
	.cm-qualifier,
	.cm-type,
	.cm-variable-3 {
		color: #2c89df;
	}

	.cm-tag {
		color: #71DB80;
	}

	.cm-number {
		color: #51a980;
	}
}
</style>
