<template>
	<div class="znpb-gradient-options-wrapper">
		<InputWrapper :title="__('Gradient type', 'zionbuilder')" class="znpb-gradient__type">
			<Tabs tab-style="minimal" :active-tab="computedValue.type" @changed-tab="onTabChange">
				<Tab name="Linear">
					<InputWrapper :title="__('Gradient angle', 'zionbuilder')" class="znpb-gradient__angle">
						<InputRange v-model="computedAngle" :min="0" :max="360" :step="1">deg</InputRange>
					</InputWrapper>
				</Tab>
				<Tab name="Radial">
					<div class="znpb-radial-postion-wrapper">
						<InputWrapper title="Position X" layout="inline">
							<InputNumber v-model="computedPositionX" :min="0" :max="100" :step="1"> % </InputNumber>
						</InputWrapper>
						<InputWrapper title="Position Y" layout="inline">
							<InputNumber v-model="computedPositionY" :min="0" :max="100" :step="1"> % </InputNumber>
						</InputWrapper>
					</div>
				</Tab>
			</Tabs>
		</InputWrapper>

		<InputWrapper :title="__('Gradient bar', 'zionbuilder')">
			<GradientBar v-model="computedValue" class="znpb-gradient__bar" />
		</InputWrapper>
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientOptions',
};
</script>

<script lang="ts" setup>
import { __ } from '@wordpress/i18n';
import { computed } from 'vue';
import GradientBar from './GradientBar.vue';
import { InputWrapper } from '../InputWrapper';
import { InputNumber } from '../InputNumber';
import { InputRange } from '../InputRange';
import { Tabs, Tab } from '../Tabs';
import type { Gradient, Position } from './GradientBar.vue';

const props = defineProps<{
	modelValue: Gradient;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: Gradient): void;
}>();

const computedValue = computed({
	get() {
		return props.modelValue;
	},
	set(newValue: Gradient) {
		emit('update:modelValue', newValue);
	},
});

const computedAngle = computed({
	get() {
		return computedValue.value.angle;
	},
	set(newValue: number) {
		computedValue.value = {
			...computedValue.value,
			angle: newValue,
		};
	},
});

const computedPosition = computed({
	get() {
		return computedValue.value.position || {};
	},
	set(newValue: Position) {
		computedValue.value = {
			...computedValue.value,
			position: newValue,
		};
	},
});

const computedPositionX = computed({
	get() {
		return computedValue.value.position?.x || 50;
	},
	set(newValue: number) {
		computedPosition.value = {
			...computedPosition.value,
			x: newValue,
		};
	},
});

const computedPositionY = computed({
	get() {
		return computedValue.value.position?.y || 50;
	},
	set(newValue: number) {
		computedPosition.value = {
			...computedPosition.value,
			y: newValue,
		};
	},
});

function onTabChange(tabId: string) {
	computedValue.value = {
		...computedValue.value,
		type: tabId,
	};
}
</script>
<style lang="scss">
.znpb-admin__wrapper {
	.znpb-gradient-options-wrapper {
		padding: 20px 0 0;
	}
}
.znpb-gradient-options-wrapper {
	.znpb-forms-input-wrapper {
		padding-right: 0;
		padding-left: 0;
	}
	& > .znpb-forms-input-wrapper > .znpb-forms-form__input-title {
		margin-bottom: 0;
	}

	.znpb-tabs > .znpb-tabs__content {
		background: transparent;
	}
}
</style>
