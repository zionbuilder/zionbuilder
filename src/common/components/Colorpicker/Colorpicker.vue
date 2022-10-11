<template>
	<div
		ref="colorPicker"
		class="znpb-form-colorpicker__color-picker-holder"
		:class="{ ['color-picker-holder--has-library']: showLibrary }"
		:style="pickerStyle"
	>
		<ColorBoard v-model:color-object="computedColorObject" />

		<div class="znpb-form-colorpicker-inner__panel">
			<PanelHex v-model:model-value="computedColorObject" />

			<slot name="end" />
		</div>
	</div>
</template>

<script lang="ts" setup>
import tinycolor from 'tinycolor2';
import PanelHex from './PanelHex.vue';
import ColorBoard from './ColorBoard.vue';
import { computed, CSSProperties } from 'vue';
import type { ColorFormats } from 'tinycolor2';

type Colors = string | ColorFormats.RGBA | ColorFormats.HSLA | ColorFormats.HSVA | ColorFormats.HSLA;

const props = withDefaults(
	defineProps<{
		model?: Colors;
		showLibrary?: boolean;
		zIndex?: number;
	}>(),
	{
		appendTo: undefined,
		showLibrary: true,
		model: '',
	},
);

const emit = defineEmits(['color-changed']);

const computedModelValue = computed({
	get() {
		return props.model;
	},
	set(newValue) {
		if (newValue) {
			emit('color-changed', newValue);
		} else {
			emit('color-changed', '');
		}
	},
});

const computedColorObject = computed({
	get() {
		return getColorObject(props.model);
	},
	set(newValue) {
		const colorObject = tinycolor(newValue);
		const format = colorObject.getFormat();
		let emittedColor;
		if (colorObject.isValid()) {
			if (format === 'hsl') {
				emittedColor = colorObject.toHslString();
			} else if (format === 'rgb' || format === 'hsv') {
				emittedColor = colorObject.toRgbString();
			} else if (format === 'hex' || format === 'hex8') {
				emittedColor = colorObject.getAlpha() < 1 ? colorObject.toHex8String() : colorObject.toHexString();
			} else if (format === 'name') {
				emittedColor = newValue;
			}
		} else {
			emittedColor = newValue;
		}

		computedModelValue.value = emittedColor;
	},
});

const pickerStyle = computed<CSSProperties>(() => {
	if (props.appendTo) {
		return {
			zIndex: props.zIndex,
		};
	}
	return {};
});

function getColorObject(model?: Colors) {
	const colorObject = tinycolor(model);
	let hsva = {
		h: 0,
		s: 0,
		v: 0,
		a: 1,
	};
	let hsla = {
		h: 0,
		s: 0,
		l: 0,
		a: 1,
	};
	let hex8 = '';
	let rgba = '';
	let hex = model ? model : '';

	let format = 'hex';
	if (colorObject.isValid()) {
		format = colorObject.getFormat();
		hsva = colorObject.toHsv();
		hsla = colorObject.toHsl();
		hex = format === 'name' ? model : colorObject.toHexString();
		hex8 = colorObject.toHex8String();
		rgba = colorObject.toRgb();
	}

	return {
		hex,
		hex8,
		rgba,
		hsla,
		hsva,
		format,
	};
}
</script>

<script lang="ts">
export default {
	name: 'ColorPicker',
	inheritAttrs: false,
};
</script>

<style lang="scss">
@import '../../scss/_mixins.scss';

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
	box-shadow: -3px 0 3px 0 rgba(0, 0, 0, 0.1);
	border-radius: 3px;
	border-bottom-left-radius: 0;
	border-top-left-radius: 0;
	cursor: pointer;
}
</style>
