<template>
	<div class="znpb-dimensions-wrapper">
		<div v-for="(dimension, i) in computedDimensions" :key="i" class="znpb-dimension" :class="`znpb-dimension--${i}`">
			<div v-if="dimension.name !== 'link'" class="znpb-dimensions_icon">
				<Icon :icon="dimension.icon"></Icon>
			</div>
			<InputNumberUnit
				v-if="dimension.name !== 'link'"
				:model-value="modelValue[dimension.id]"
				:title="dimension.id"
				:min="min"
				:max="max"
				:step="1"
				:placeholder="placeholder ? placeholder[dimension.id] : ''"
				@update:modelValue="onValueUpdated(dimension.id, $event)"
				@linked-value="handleLinkValues"
			/>
			<div v-if="dimension.name === 'link'" class="znpb-dimensions__center">
				<Icon
					:icon="linked ? 'link' : 'unlink'"
					:title="linked ? 'Unlink' : 'Link'"
					class="znpb-dimensions__link"
					:class="{ ['znpb-dimensions__link--linked']: linked }"
					@click="handleLinkValues"
				></Icon>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputDimensions',
};
</script>

<script lang="ts" setup>
import { ref, computed } from 'vue';

import { InputNumberUnit } from '../InputNumber';

type Dimensions = Record<string, string>;
type Dimension = {
	name: string;
	id: string;
	icon?: string;
};

const props = withDefaults(
	defineProps<{
		modelValue?: Dimensions;
		dimensions: Dimension[];
		min?: number;
		max?: number;
		placeholder?: Dimensions;
	}>(),
	{
		modelValue() {
			return {};
		},
		min: 0,
		max: Infinity,
		placeholder() {
			return {};
		},
	},
);

const emit = defineEmits(['update:modelValue']);

const linked = ref(false);
const computedDimensions = computed(() => {
	return [
		...props.dimensions,
		{
			name: 'link',
			id: 'link',
		},
	];
});

function handleLinkValues() {
	linked.value = !linked.value;

	if (linked.value) {
		// Check to see if we already have a saved value
		const dimensionsIDs = props.dimensions.map(dimension => dimension.id);
		const savedPositionValue = Object.keys(props.modelValue).find(
			position => dimensionsIDs.includes(position) && typeof props.modelValue[position] !== 'undefined',
		);

		if (savedPositionValue) {
			onValueUpdated('', props.modelValue[savedPositionValue]);
		}
	}
}

function onValueUpdated(position: string, newValue: string) {
	/**
	 * emits new object with new value of borders
	 */
	if (linked.value) {
		const valuesToUpdate = props.dimensions.filter(dimension => {
			return dimension.id !== 'link';
		});
		let values: Record<string, string> = {};
		valuesToUpdate.forEach(value => {
			values[value.id] = newValue;
		});
		emit('update:modelValue', {
			...props.modelValue,
			...values,
		});
	} else {
		emit('update:modelValue', {
			...props.modelValue,
			[position]: newValue,
		});
	}
}
</script>

<style lang="scss">
.znpb-dimensions-wrapper {
	display: grid;

	grid-template-areas:
		'a b c'
		'd b e';
	.znpb-dimensions__link {
		color: var(--zb-surface-icon-color);
		background-color: var(--zb-surface-lighter-color);
		transition: color 0.15s;

		&:hover {
			color: var(--zb-surface-text-hover-color);
		}
	}
	.znpb-dimensions__link.znpb-dimensions__link--linked {
		color: var(--zb-secondary-text-color);
		background-color: var(--zb-secondary-color);
	}
}
.znpb-dimensions__center {
	display: flex;
	justify-content: center;
	align-items: center;
	flex: 2;
	.znpb-editor-icon-wrapper {
		display: inline-flex;
		justify-content: center;
		align-items: center;
		width: 40px;
		height: 40px;
		border-radius: 3px;
		cursor: pointer;
	}
}

.znpb-dimensions-wrapper > .znpb-tabs__content {
	padding-top: 15px;
}
.znpb-dimension {
	display: flex;
	align-items: center;
	margin: 0 0 10px;
	&--0 {
		.znpb-dimensions_icon {
			margin-right: 10px;
		}
	}
	&--1 {
		flex-direction: row-reverse;
		.znpb-dimensions_icon {
			margin-left: 10px;
		}
	}
	&--4 {
		margin: 10px;

		grid-area: b;
	}
	&--2 {
		.znpb-dimensions_icon {
			margin-right: 10px;
		}
	}
	&--3 {
		flex-direction: row-reverse;
		.znpb-dimensions_icon {
			margin-left: 10px;
		}
	}
}
</style>
