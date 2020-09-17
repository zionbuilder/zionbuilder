<template>
	<div
		class="zion-input"
		:class="{
			'zion-input--has-prepend': $slots.prepend,
			'zion-input--has-append': $slots.append,
			'zion-input--has-suffix': hasSuffixContent,
			'zion-input--error': error,
			[`zion-input--size-${size}`]: size
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

		<BaseIcon
			class="zion-input__suffix-icon zion-input__clear-text"
			icon="close"
			v-if="showClear"
			@mousedown.native.stop="inputValue = ''"
		/>

		<div
			class="zion-input__suffix"
			v-if="$slots.suffix || icon || $slots.append"
		>
			<!-- @slot Content that will be placed after input -->
			<slot name="suffix"></slot>

			<BaseIcon
				class="zion-input__suffix-icon"
				:icon="icon"
				v-if="icon"
				@click.native="inputValue = ''"
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
import BaseIcon from '@/common/components/BaseIcon.vue'

export default {
	name: 'BaseInput',
	components: {
		BaseIcon
	},
	inheritAttrs: false,
	props: {
		/**
		 * v-model/value for the input
		 */
		value: {
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
		}
	},
	data () {
		return {
			localValue: this.value || ''
		}
	},

	computed: {
		showClear () {
			return this.clearable && this.localValue && this.localValue.length > 0
		},
		hasSuffixContent () {
			return this.icon || this.showClear
		},
		inputValue: {
			get () {
				return this.value !== 'undefined' ? this.value : null
			},
			set (newValue) {
				/** Updates the input value for the v-model **/
				this.$emit('input', newValue)
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

	input, textarea {
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

		&:focus, &:active, &:visited {
			box-shadow: none;
			outline: 0;
		}
	}
	input {
		max-height: 42px;
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
	&__prepend, &__append {
		display: flex;
		align-items: center;
		height: 100%;
		padding: 0 13px;
		color: $surface-headings-color;
	}

	&__prefix, &__suffix {
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
		input, textarea {
			border-bottom-left-radius: 0;
			border-top-left-radius: 0;
		}
	}

	&--has-append {
		input, textarea {
			border-top-right-radius: 0;
			border-bottom-right-radius: 0;
		}
	}
	&--size-narrow &__suffix-icon {
		margin: 14px 7px;
	}

	&--size-narrow &__prepend, &--size-narrow &__append {
		padding: 0 7px;
	}

	&--size-big input {
		padding: 20px;
	}
}
</style>
