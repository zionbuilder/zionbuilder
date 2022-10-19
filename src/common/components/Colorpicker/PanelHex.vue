<template>
	<div class="znpb-colorpicker-inner-editor">
		<div class="znpb-colorpicker-inner-editor__colors">
			<div class="znpb-colorpicker-inner-editor__current-color">
				<span class="znpb-colorpicker-circle znpb-colorpicker-circle--opacity">
					<span
						:style="{ backgroundColor: modelValue.hex8 }"
						class="znpb-colorpicker-circle znpb-colorpicker-circle-color"
					/>
				</span>
			</div>
			<div class="znpb-colorpicker-inner-editor__stripes">
				<HueStrip v-model="hslaValue" />
				<OpacityStrip v-model="hslaValue" />
			</div>
		</div>
		<div class="znpb-colorpicker-inner-editor__rgba">
			<RgbaElement v-if="modelValue.format === 'rgb'" v-model="rgbaValue" />

			<HslaElement v-if="modelValue.format === 'hsl'" v-model="hslaValue" />
			<HexElement
				v-if="modelValue.format === 'hex' || modelValue.format === 'hex8' || modelValue.format === 'name'"
				v-model="hexValue"
			/>
			<div class="znpb-color-picker-change-color znpb-input-number-arrow-wrapper">
				<Icon icon="select" :rotate="180" class="znpb-arrow-increment" @click="changeHex"></Icon>
				<Icon icon="select" class="znpb-arrow-decrement" @click="changeHexback"></Icon>
			</div>
		</div>
	</div>
</template>

<script lang="ts" setup>
import { Icon } from '../Icon';
import HueStrip from './HueStrip.vue';
import OpacityStrip from './OpacityStrip.vue';
import RgbaElement from './RgbaElement.vue';
import HslaElement from './HslaElement.vue';
import HexElement from './HexElement.vue';
import { computed } from 'vue';
import type { ColorFormats } from 'tinycolor2';

type Format = 'hex' | 'hsl' | 'rgb' | 'hex8' | 'name';
const props = defineProps<{
	modelValue: {
		format: Format;
		hex: string;
		hex8: string;
		hsla: ColorFormats.HSLA;
		hsva: ColorFormats.HSVA;
		rgba: ColorFormats.RGBA;
	};
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: string | ColorFormats.RGBA | ColorFormats.HSLA): void;
	(e: 'update:format', format: Format): void;
}>();

const hexValue = computed({
	get() {
		return props.modelValue.format === 'hex8' ? props.modelValue.hex8 : props.modelValue.hex;
	},
	set(newValue: string) {
		emit('update:modelValue', newValue);
	},
});

const hslaValue = computed({
	get() {
		return props.modelValue.hsla;
	},
	set(hsla: ColorFormats.HSLA) {
		emit('update:modelValue', hsla);
	},
});

const rgbaValue = computed({
	get() {
		return props.modelValue.rgba;
	},
	set(rgba: ColorFormats.RGBA) {
		emit('update:modelValue', rgba);
	},
});

function changeHex() {
	if (props.modelValue.format === 'hex' || props.modelValue.format === 'hex8' || props.modelValue.format === 'name') {
		emit('update:modelValue', props.modelValue.hsla);
	} else if (props.modelValue.format === 'hsl') {
		emit('update:modelValue', props.modelValue.rgba);
	} else if (props.modelValue.format === 'rgb') {
		emit('update:modelValue', props.modelValue.hex);
	}
}

function changeHexback() {
	if (props.modelValue.format === 'hsl') {
		emit('update:modelValue', props.modelValue.hex);
	} else if (props.modelValue.format === 'rgb') {
		emit('update:modelValue', props.modelValue.hsla);
	} else if (
		props.modelValue.format === 'hex' ||
		props.modelValue.format === 'hex8' ||
		props.modelValue.format === 'name'
	) {
		emit('update:modelValue', props.modelValue.rgba);
	}
}
</script>

<style lang="scss">
.znpb-colorpicker-inner-editor {
	display: flex;
	flex-direction: column;
	flex-shrink: 0;
	padding: 20px 20px 12px;
	background-color: var(--zb-surface-color);

	&__colors {
		display: flex;
		margin-bottom: 15px;
	}

	&__current-color {
		width: 30px;
		height: 30px;

		.znpb-colorpicker-circle {
			display: block;
			width: 100%;
			height: 100%;
		}
	}

	&__stripes {
		flex: 1;
		margin-left: 10px;
	}

	&__hue,
	&__opacity {
		position: relative;
		width: 100%;
		height: 8px;
	}
	.znpb-colorpicker-circle--opacity {
		position: relative;

		.znpb-colorpicker-circle {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			box-shadow: inset 0 0 0 2px rgba(0, 0, 0, 0.1);
		}
	}

	&__rgba {
		display: flex;
		justify-content: space-between;

		.zion-icon.zion-picker {
			align-items: center;
			height: 30px;
			padding: 0 3px;
			font-size: 10px;
		}

		.znpb-color-picker-change-color {
			margin: 3px 0 0 10px;
			background: none;
			box-shadow: none;

			.znpb-arrow-increment,
			.znpb-arrow-decrement {
				width: 12px;
				margin: 0;
				color: var(--zb-surface-icon-color);
				background: none;
				border-radius: 0;
				transition: color 0.1s;

				&:hover {
					color: var(--zb-surface-text-hover-color);
				}
			}
		}
	}
}
</style>
