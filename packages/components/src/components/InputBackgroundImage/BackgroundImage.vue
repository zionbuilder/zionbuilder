<template>
	<div class="znpb-input-background-image">
		<InputImage
			:modelValue="computedValue['background-image']"
			:should-drag-image="true"
			:position-top="backgroundPositionYModel"
			:position-left="backgroundPositionXModel"
			@update:modelValue="onOptionUpdated('background-image', $event)"
			@background-position-change="changeBackgroundPosition"
		/>

		<OptionsForm v-model="computedValue" :schema="backgroundImageSchema" />
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputBackgroundImage',
};
</script>

<script lang="ts" setup>
import { computed } from 'vue';
import InputImage from '../InputImage/InputImage.vue';
import { useOptionsSchemas } from '../../composables/useOptionsSchemas';

interface BackgroundImage {
	'background-image'?: string;
	'background-position-x'?: string;
	'background-position-y'?: string;
	'background-repeat'?: string;
	'background-attachment'?: string;
	'background-size'?: string;
	'background-clip'?: string;
	'background-blend-mode'?: string;
}

const props = defineProps<{
	modelValue: BackgroundImage;
}>();

const emit = defineEmits(['update:modelValue']);
const { getSchema } = useOptionsSchemas();
const backgroundImageSchema = getSchema('backgroundImageSchema');
console.log('backgroundImageSchema', backgroundImageSchema);

const computedValue = computed({
	get() {
		return props.modelValue || {};
	},
	set(newValue: BackgroundImage) {
		emit('update:modelValue', newValue);
	},
});

const backgroundPositionXModel = computed(() => computedValue.value['background-position-x']);

const backgroundPositionYModel = computed(() => computedValue.value['background-position-y']);

function changeBackgroundPosition(position: { x: number; y: number }) {
	emit('update:modelValue', {
		...computedValue.value,
		'background-position-x': `${position.x}%`,
		'background-position-y': `${position.y}%`,
	});
}

function onOptionUpdated(optionId: keyof BackgroundImage, newValue: string) {
	const newValues = {
		...props.modelValue,
	};

	newValues[optionId] = newValue;
	computedValue.value = newValues;
}
</script>

<style lang="scss">
.znpb-input-background-image {
	&-position-wrapper {
		display: flex;

		label {
			&:nth-child(1) {
				margin-right: 15px;
			}
		}
	}
}
</style>
