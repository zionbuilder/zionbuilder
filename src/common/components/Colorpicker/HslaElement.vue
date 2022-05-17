<template>
	<div class="znpb-colorpicker-inner-editor-hsla">
		<InputLabel>
			<InputNumber :modelValue="hsla.h" :min="0" :max="360" :step="1" @update:modelValue="updateHex('h', $event)" />
			H
		</InputLabel>
		<InputLabel class="znpb-colorpicker-inner-editor__number--has-percentage">
			<InputNumber v-model="hsla.s" :min="0" :max="100" :step="1" @update:modelValue="updateHex('s', $event)">
				<span class="znpb-colorpicker-inner-editor__number-unit">%</span>
			</InputNumber>
			S
		</InputLabel>
		<InputLabel class="znpb-colorpicker-inner-editor__number--has-percentage">
			<InputNumber v-model="hsla.l" :min="0" :max="100" :step="1" @update:modelValue="updateHex('l', $event)">
				<span class="znpb-colorpicker-inner-editor__number-unit">%</span>
			</InputNumber>
			L
		</InputLabel>
		<InputLabel>
			<InputNumber v-model="hsla.a" :min="0" :max="1" :step="0.01" @update:modelValue="updateHex('a', $event)" />
			A
		</InputLabel>
	</div>
</template>

<script lang="ts" setup>
import { InputNumber } from '../InputNumber';
import { InputLabel } from '../InputLabel';
import { computed } from 'vue';
import type { ColorFormats } from 'tinycolor2';

const props = defineProps<{
	modelValue: ColorFormats.HSLA;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: ColorFormats.HSLA): void;
}>();

const hsla = computed(() => {
	const { h, s, l, a } = props.modelValue;
	return {
		h: Number(h.toFixed()),
		s: Number((s * 100).toFixed()),
		l: Number((l * 100).toFixed()),
		a,
	};
});

function updateHex(property: keyof ColorFormats.HSLA, newValue: number) {
	const value = property === 's' || property === 'l' ? newValue / 100 : newValue;

	emit('update:modelValue', {
		...props.modelValue,
		[property]: value,
	});
}
</script>

<style lang="scss">
.znpb-colorpicker-inner-editor-hsla {
	.znpb-form-element {
		&:last-child {
			margin-right: 0;
		}
	}
}

.znpb-colorpicker-inner-editor__number {
	&--has-percentage .znpb-input-number {
		font-size: 13px;
		line-height: 1;

		.zion-input {
			font-size: 13px;
		}
		input {
			flex-grow: 1;
			width: 50%;
			font-size: 13px;
			text-align: right;
		}

		.zion-input__suffix {
			justify-content: left;
			flex-grow: 1;
		}
	}

	&-unit {
		padding: 5px 5px 5px 0;
	}
}
</style>
