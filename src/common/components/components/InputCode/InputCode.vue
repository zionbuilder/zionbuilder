<template>
	<div class="znpb-custom-code">
		<textarea
			ref="codeMirrorTextarea"
			:value="modelValue"
			class="znpb-custom-code__text-area"
			:placeholder="placeholder"
		></textarea>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputCode',
};
</script>

<script lang="ts" setup>
import { ref, Ref, watch, onMounted } from 'vue';
import type { Editor } from 'codemirror';

const props = withDefaults(
	defineProps<{
		placeholder?: string;
		mode?: string;
		modelValue?: string;
	}>(),
	{
		modelValue: '',
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string): void;
}>();

let editor: Editor;
const codeMirrorTextarea: Ref<HTMLTextAreaElement | null> = ref(null);
let ignoreChange = false;

watch(
	() => props.modelValue,
	newValue => {
		if (editor?.getValue() !== newValue) {
			ignoreChange = true;
			editor.setValue(newValue);
		}
	},
);

function onEditorChange(instance: Editor) {
	if (!ignoreChange) {
		emit('update:modelValue', instance.getValue());
	}
	ignoreChange = false;
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
].includes(props.mode || '');

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
			bothTags: true,
		},
		csslint: {
			errors: true,
			'box-model': true,
			'display-property-grouping': true,
			'duplicate-properties': true,
			'known-properties': true,
			'outline-none': true,
		},
		jshint: {
			boss: true,
			curly: true,
			eqeqeq: true,
			eqnull: true,
			es3: true,
			expr: true,
			immed: true,
			noarg: true,
			nonbsp: true,
			onevar: true,
			quotmark: 'single',
			trailing: true,
			undef: true,
			unused: true,
			browser: true,
			globals: {
				_: false,
				Backbone: false,
				jQuery: false,
				JSON: false,
				wp: false,
			},
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
			'attr-unsafe-chars': true,
		},
		// Show lint error numbers
		gutters: ['CodeMirror-lint-markers'],
	});

	// Set the editor value
	if (props.modelValue) {
		// editor.setValue(this.modelValue)
	}

	// Bind events
	editor.on('change', onEditorChange);
});
</script>

<style lang="scss">
.znpb-custom-code {
	&__text-area {
		width: 100%;
	}

	.CodeMirror {
		position: relative;
		overflow: hidden;
		color: #98a1ab;
		background: var(--zb-surface-darker-color);

		pre {
			color: #98a1ab;
			line-height: 1.4;
		}

		&-gutters {
			background-color: var(--zb-surface-lighter-color);
			border-right: 1px solid var(--zb-surface-border-color);
		}

		&-linenumber {
			color: #98a1ab;
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
	.cm-variable-3,
	.cm-s-default .cm-string {
		color: #2c89df;
	}

	.cm-s-default .cm-def,
	.cm-s-default .cm-attribute {
		color: #98a1ab;
	}

	.cm-s-default .cm-comment {
		color: #525252;
	}

	.cm-invalidchar,
	.cm-s-default .cm-error {
		color: #c15050;
	}

	.cm-tag {
		color: #71db80;
	}

	.cm-number {
		color: #51a980;
	}
}
</style>
