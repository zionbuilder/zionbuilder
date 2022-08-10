<template>
	<Teleport :disabled="!appendTo" :to="appendTo">
		<div
			ref="colorPicker"
			class="znpb-form-colorpicker__color-picker-holder"
			:class="{ ['color-picker-holder--has-library']: showLibrary }"
			:style="pickerStyle"
			v-bind="$attrs"
		>
			<ColorBoard :modelValue="(color as ColorObject)" @update:modelValue="updateColor" />

			<div class="znpb-form-colorpicker-inner__panel">
				<PanelHex :modelValue="(color as ColorObject)" @update:modelValue="updateColor" @update:format="updateFormat" />

				<slot name="end" />
			</div>
		</div>
	</Teleport>
</template>

<script lang="ts">
export default {
	name: 'ColorPicker',
	inheritAttrs: false,
};
</script>

<script lang="ts" setup>
import tinycolor from 'tinycolor2';
import PanelHex from './PanelHex.vue';
import ColorBoard from './ColorBoard.vue';
import { ref, computed, Ref, watch, CSSProperties } from 'vue';
import type { ColorFormats } from 'tinycolor2';

type Format = 'hex' | 'hsv' | 'hsl' | 'rgb' | 'hex8' | 'name' | false;

type Colors = string | ColorFormats.RGBA | ColorFormats.HSLA | ColorFormats.HSVA | ColorFormats.HSLA;

type ColorObject = ReturnType<typeof getColorObject>;

const props = withDefaults(
	defineProps<{
		model?: Colors;
		showLibrary?: boolean;
		appendTo?: string;
		zIndex?: number;
	}>(),
	{
		appendTo: undefined,
		showLibrary: true,
	},
);

const emit = defineEmits(['color-changed']);

const oldHue = ref(0);
const color: Ref<ColorObject | undefined> = ref();
const format: Ref<Format | undefined> = ref();
const localLibrary = ref(props.showLibrary);

color.value = getColorObject(props.model);
format.value = color.value.format;

watch(
	() => props.model,
	(newVal?: Colors) => {
		color.value = getColorObject(newVal);
	},
);

const pickerStyle = computed<CSSProperties>(() => {
	if (props.appendTo) {
		return {
			zIndex: props.zIndex,
		};
	}
	return {};
});

function updateFormat(currentFormat: Format) {
	format.value = currentFormat;
}

function updateColor(newValue: Colors) {
	oldHue.value = typeof newValue !== 'string' && 'h' in newValue ? newValue.h : oldHue.value;
	const colorObject = tinycolor(newValue);

	let emittedColor;
	if (format.value === 'hsl') {
		emittedColor = colorObject.toHslString();
	} else if (format.value === 'rgb' || format.value === 'name' || format.value === 'hsv') {
		emittedColor = colorObject.toRgbString();
	} else if (format.value === 'hex' || format.value === 'hex8') {
		emittedColor = colorObject.getAlpha() < 1 ? colorObject.toHex8String() : colorObject.toHexString();
	}

	color.value = getColorObject(newValue);

	if (newValue) {
		emit('color-changed', emittedColor);
	} else {
		emit('color-changed', '');
	}
}

function getColorObject(model?: Colors) {
	const colorObject = tinycolor(model);

	let hsva = colorObject.toHsv();
	let hsla = colorObject.toHsl();
	let hex = colorObject.toHexString();
	let hex8 = colorObject.toHex8String();
	let rgba = colorObject.toRgb();

	// If we do not have a model, set empty values
	if (!model) {
		hex = '';
		hex8 = '';
	}

	if (hsla.s === 0) {
		hsva.h = hsla.h = oldHue.value;
	}

	if (!format.value) {
		format.value = colorObject.getFormat() as Format;
	}

	if (format.value === 'hex' || format.value === 'hex8') {
		format.value = colorObject.getAlpha() < 1 ? 'hex8' : 'hex';
	}

	if (format.value === 'name' || format.value === 'hsv' || format.value === false) {
		format.value = 'hex';
	}

	return {
		hex,
		hex8,
		rgba,
		hsla,
		hsva,
		format: format.value,
	};
}

function addGlobal(name: string) {
	let globalColor = {
		id: name.split(' ').join('_'),
		color: props.model,
		name: name,
	};
	localLibrary.value = true;
	addGlobalColor(globalColor);
}
</script>

<style lang="scss">
@import "/@/common/scss/_mixins.scss";
.znpb-form-colorpicker__color-picker-holder {
	overflow: hidden;
	min-width: 280px;
	max-height: 382px;
	border-radius: 3px;
	user-select: none;
}
.znpb-form-colorpicker-inner__panel {
	position: relative;
	display: flex;
	flex-direction: column;
	overflow: hidden;
	max-width: 280px;
	border-bottom-right-radius: 3px;
	// background-color: var(--zb-surface-lighter-color);
	border-bottom-left-radius: 3px;

	.znpb-preset-input-wrapper {
		background: #fff;
		.znpb-editor-icon-wrapper {
			cursor: pointer;
		}
	}
}
.znpb-colorpicker {
	&-circle {
		position: relative;
		flex-shrink: 0;
		width: 14px;
		height: 14px;
		border-radius: 2px;
		cursor: pointer;
		&-color {
			display: block;
		}
		&--opacity {
			@extend %opacitybg;
		}
	}
	&-add-color {
		display: flex;
		justify-content: center;
		align-items: center;
		border: 2px dashed var(--zb-surface-border-color);
	}
}
.znpb-input-number-arrow-wrapper {
	display: flex;
	flex-direction: column;
	color: var(--zb-surface-icon-color);
	background-color: var(--zb-surface-lighter-color);
	box-shadow: -3px 0 3px 0 rgba(0, 0, 0, .1);
	border-radius: 3px;
	border-bottom-left-radius: 0;
	border-top-left-radius: 0;
	cursor: pointer;
}
</style>
