<template>
	<div
		class="znpb-custom-code"
		@keydown="onKeyDown"
	>
		<textarea
			class="znpb-custom-code__text-area"
			:id="codeEditor"
			v-bind="$attrs"
		></textarea>
	</div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
	name: 'CustomCode',
	inheritAttrs: false,
	data () {
		return {
			editor: {},
			codeModel: {},
			codeEditor: `${this.mode}Editor`
		}
	},
	props: {
		mode: {
			type: String
		},
		value: {
			type: String,
			required: false,
			default () {
				return ''
			}
		},
		shouldUpdate: {
			type: String
		}
	},
	methods: {
		onKeyDown (e) {
			if (e.shiftKey) {
				e.stopPropagation()
			}
		},
		onEditorChange (instance, changeObj) {
			this.$emit('input', instance.getValue())
		}
	},
	watch: {
		shouldUpdate () {
			this.editor.setValue(this.value)
		}
	},
	computed: {
		...mapGetters([
			'getElementData'
		]),
		getValue () {
			return this.editor.getValue()
		}
	},
	mounted () {
		this.editor = window.wp.CodeMirror.fromTextArea(document.getElementById(this.codeEditor), {
			mode: this.mode,
			lineNumbers: true,
			lint: true,
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
		if (this.value) {
			this.editor.setValue(this.value)
		}

		// Bind events
		this.editor.on('change', this.onEditorChange)
	}
}
</script>

<style lang="scss">
.znpb-custom-code {
	&__text-area {
		width: 100%;
	}

	& .CodeMirror pre {
		color: #a9a9a9;
	}
}
.znpb-custom-code * {
	box-sizing: content-box !important;
}
</style>
