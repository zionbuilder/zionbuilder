<template>
	<div
		class="zion-input"
		:class="{
			'zion-input--has-prepend': $slots.prepend,
			'zion-input--has-append': $slots.append,
			'zion-input--has-suffix': hasSuffixContent,
			'zion-input--error': error,
			[`zion-input--size-${size}`]: size,
			[cssClass]: cssClass,
		}"
		@keydown="onKeyDown"
	>
		<div v-if="$slots.prepend" class="zion-input__prefix">
			<div v-if="$slots.prepend" class="zion-input__prepend">
				<!-- @slot Content that will be placed before input -->
				<slot name="prepend"></slot>
			</div>
		</div>
		<input
			v-if="type !== 'textarea'"
			ref="input"
			:type="type"
			:value="modelValue"
			:style="getStyle"
			v-bind="$attrs"
			@input="onInput"
		/>
		<textarea
			v-else
			ref="input"
			class="znpb-fancy-scrollbar"
			:value="modelValue"
			v-bind="$attrs"
			@input="$emit('update:modelValue' ,($event.target as HTMLTextAreaElement).value)"
		>
		</textarea>

		<slot name="after-input"></slot>

		<Icon
			v-if="showClear"
			class="zion-input__suffix-icon zion-input__clear-text"
			icon="close"
			@mousedown.stop.prevent="$emit('update:modelValue', '')"
		/>

		<div v-if="$slots.suffix || icon || $slots.append" class="zion-input__suffix">
			<!-- @slot Content that will be placed after input -->
			<slot name="suffix"></slot>

			<Icon
				v-if="icon"
				class="zion-input__suffix-icon"
				:icon="icon"
				@click.stop.prevent="$emit('update:modelValue', '')"
			/>

			<div v-if="$slots.append" class="zion-input__append">
				<!-- @slot Content that will be appended to input -->
				<slot name="append"></slot>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'BaseInput',
	inheritAttrs: false,
};
</script>

<script lang="ts" setup>
import { ref, computed, CSSProperties } from 'vue';
import { Icon } from '../Icon';

const props = withDefaults(
	defineProps<{
		/**
		 * v-model/modelValue for the input
		 */
		modelValue?: string | number;

		/**
		 * If true, will mark the field as red
		 */
		error?: boolean;
		/**
		 * HTML input type (email, password, etc)
		 */
		type?: string;
		/**
		 * Icon that appears at the end of the input
		 */
		icon?: string;
		/**
		 * whether to show clear button
		 */
		clearable?: boolean;
		/**
		 * Input size. Can be one of "narrow", "big"
		 */
		size?: string;

		fontFamily?: string;

		class?: CSSProperties;
	}>(),
	{
		modelValue: '',
		error: false,
		name: 'BaseInput',
		type: 'text',
		clearable: false,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: string | number): void;
}>();

// Template Ref
const input = ref<HTMLInputElement | null>(null);

const showClear = computed(() => {
	return props.clearable && props.modelValue ? true : false;
});

const hasSuffixContent = computed(() => {
	return props.icon || showClear.value;
});

const getStyle = computed(() => {
	return {
		fontFamily: props.fontFamily || '',
	};
});

const cssClass = computed(() => {
	return props.class;
});

function onKeyDown(e: KeyboardEvent) {
	if (e.shiftKey) {
		e.stopPropagation();
	}
}

function focus() {
	input.value?.focus();
}

function blur() {
	input.value?.blur();
}

function onInput(e: Event) {
	// This is needed because HTML input element send empty string if the value is not valid ( f.e. you start typing a dot for decimals )
	if (props.type === 'number' && e.target.validity.badInput) {
		return;
	}

	emit('update:modelValue', (e.target as HTMLInputElement).value);
}

defineExpose({
	input,
	focus,
	blur,
});
</script>

<style lang="scss">
@import '../../scss/_mixins.scss';
body {
	.zion-input {
		position: relative;
		display: flex;
		width: 100%;
		font-family: var(--zb-font-stack);
		font-size: 13px;
		line-height: 1;
		background: var(--zb-input-bg-color);
		border: 2px solid var(--zb-input-border-color);
		border-radius: 3px;
		transition: border 0.3s;

		.znpb-editor-icon-wrapper {
			color: var(--zb-input-icon-color);
			font-size: 14px;
		}

		&--error {
			--zb-input-border-color: red;
		}

		&__clear-text {
			cursor: pointer;
		}

		input,
		textarea {
			width: 100%;
			height: auto;
			padding: 10.5px 12px;
			margin: 0;
			color: var(--zb-surface-text-color);
			font-family: var(--zb-font-stack);
			font-size: 13px;
			font-weight: 500;
			line-height: 1;
			background: var(--zb-input-bg-color);
			background-image: none;
			box-shadow: none;
			border: 0;
			border-radius: 3px;

			-webkit-appearance: none;

			&:focus,
			&:active,
			&:visited {
				color: var(--zb-input-text-color);
				background: var(--zb-input-bg-color);
				box-shadow: none;
				outline: 0;
			}
		}
		input {
			max-height: 42px;
			min-width: 0;
			border: none;
			// prevent other themes to add their own style uppon input fixed height
		}
		input::placeholder {
			color: var(--zb-input-placeholder-color);
		}
		textarea {
			line-height: 1.8;
		}
		&__prepend {
			@include border(3px, 0, 0, 3px);
		}
		&__append {
			@include border(0, 3px, 3px, 0);
		}
		&__prepend,
		&__append {
			display: flex;
			align-items: center;
			height: 100%;
			padding: 0 13px;
			color: var(--zb-surface-icon-color);
		}

		&__prefix,
		&__suffix {
			display: flex;
			justify-content: center;
			align-items: center;
			line-height: 1;
		}

		&__suffix-icon {
			margin: 11px;
			margin-left: 0;
		}

		&--has-prepend {
			input,
			textarea {
				border-bottom-left-radius: 0;
				border-top-left-radius: 0;
			}
		}

		&--has-append {
			input,
			textarea {
				border-top-right-radius: 0;
				border-bottom-right-radius: 0;
			}
		}
		&--size-narrow &__suffix-icon {
			margin: 14px 7px;
		}

		&--size-narrow &__prepend,
		&--size-narrow &__append {
			padding: 0 7px;
		}

		&--size-big input {
			padding: 20px;
		}
	}
}
</style>
