<template>

	<div
		class="zion-input"
		:class="{
			'zion-input--has-prepend': $slots.prepend,
			'zion-input--has-append': $slots.append,
			'zion-input--has-suffix': hasSuffixContent,
			'zion-input--error': error,
			[`zion-input--size-${size}`]: size,
			[cssClass]: cssClass
		}"
		@keydown="onKeyDown"
	>
		<div
			class="zion-input__prefix"
			v-if="$slots.prepend"
		>
			<div
				class="zion-input__prepend"
				v-if="$slots.prepend"
			>
				<!-- @slot Content that will be placed before input -->
				<slot name="prepend"></slot>
			</div>
		</div>
		<input
			v-if="type !== 'textarea'"
			:type="type"
			v-model="inputValue"
			ref="input"
			v-bind="$attrs"
			:style="getStyle"
		>
		<textarea
			class="znpb-fancy-scrollbar"
			v-else
			v-model="inputValue"
			ref="input"
			v-bind="$attrs"
		>
		</textarea>

		<slot name="after-input"></slot>

		<Icon
			class="zion-input__suffix-icon zion-input__clear-text"
			icon="close"
			v-if="showClear"
			@mousedown.stop.prevent="inputValue = ''"
		/>

		<div
			class="zion-input__suffix"
			v-if="$slots.suffix || icon || $slots.append"
		>
			<!-- @slot Content that will be placed after input -->
			<slot name="suffix"></slot>

			<Icon
				class="zion-input__suffix-icon"
				:icon="icon"
				v-if="icon"
				@click.stop.prevent="inputValue = ''"
			/>

			<div
				class="zion-input__append"
				v-if="$slots.append"
			>
				<!-- @slot Content that will be appended to input -->
				<slot name="append"></slot>
			</div>
		</div>
	</div>
</template>

<script>
import Icon from '../Icon/Icon.vue'

export default {
	name: 'BaseInput',
	components: {
		Icon
	},
	inheritAttrs: false,
	props: {
		/**
		 * v-model/modelValue for the input
		 */
		modelValue: {
			required: false,
			type: null
		},
		/**
		 * If true, will mark the field as red
		 */
		error: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		 * HTML input type (email, password, etc)
		 */
		type: {
			type: String,
			required: false,
			default: 'text'
		},
		/**
		 * Icon that appears at the end of the input
		 */
		icon: {
			type: String,
			required: false
		},
		/**
		 * whether to show clear button
		 */
		clearable: {
			type: Boolean,
			required: false,
			default: false
		},
		/**
		 * Input size. Can be one of "narrow", "big"
		 */
		size: {
			type: String,
			required: false
		},
		fontFamily: {
			type: String,
			required: false
		},
		class: {

		}
	},
	data () {
		return {
			localValue: this.modelValue || ''
		}
	},

	computed: {
		cssClass () {
			return this.class
		},
		showClear () {
			return this.clearable && this.localValue && this.localValue.length > 0
		},
		hasSuffixContent () {
			return this.icon || this.showClear
		},
		inputValue: {
			get () {
				return this.modelValue !== 'undefined' ? this.modelValue : ''
			},
			set (newValue) {
				/** Updates the input value for the v-model **/
				this.$emit('update:modelValue', newValue)
				this.localValue = newValue
			}
		},
		getStyle () {
			let style = {
				fontFamily: this.fontFamily ? this.fontFamily : null
			}
			return style
		}
	},
	methods: {
		onKeyDown (e) {
			if (e.shiftKey) {
				e.stopPropagation()
			}
		},
		focus () {
			this.$refs.input.focus()
		},
		blur () {
			this.$refs.input.blur()
		}

	}

}
</script>
<style lang="scss">
body {
	.zion-input {
		position: relative;
		display: flex;
		width: 100%;
		font-family: $font-stack;
		font-size: 13px;
		line-height: 1;
		background: transparent;
		border: 2px solid var(--zion-border-color);
		border-radius: 3px;

		.znpb-editor-icon-wrapper {
			color: $surface-headings-color;
			font-size: 14px;
		}

		&--error {
			--zion-border-color: red;
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
			color: $font-color;
			font-family: $font-stack;
			font-size: 13px;
			font-weight: 500;
			line-height: 1;
			background: transparent;
			background-image: none;
			box-shadow: none;
			border: 0;
			border-radius: 3px;

			-webkit-appearance: none;

			&:focus,
			&:active,
			&:visited {
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
			color: $surface-headings-color;
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
			color: $surface-headings-color;
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
