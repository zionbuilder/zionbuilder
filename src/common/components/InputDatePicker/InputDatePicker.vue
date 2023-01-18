<template>
	<vueDatePick
		v-model="valueModel"
		class="znpb-input-date"
		:next-month-caption="__('Next Month', 'zionbuilder')"
		:previous-month-caption="__('Previous month', 'zionbuilder')"
		:set-time-caption="__('Set time', 'zionbuilder')"
		:weekdays="weekdaysStrings"
		:months="monthsStrings"
		:pick-time="pickTime"
		:use-12-hour-clock="use12HourClock"
		:format="format"
		:is-date-disabled="disableDate"
	>
		<template #default="{ toggle }">
			<BaseInput
				v-model="valueModel"
				:readonly="readonly"
				class="znpb-input-number__input"
				v-bind="$attrs"
				@keydown="toggle"
				@mouseup="toggle"
			>
			</BaseInput>
		</template>
	</vueDatePick>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { __ } from '@wordpress/i18n';
import vueDatePick from './src/vueDatePick.vue';
import BaseInput from '../BaseInput/BaseInput.vue';

const props = withDefaults(
	defineProps<{
		/**
		 * Value for input
		 */
		modelValue: string;
		readonly?: boolean;
		pickTime?: boolean;
		format?: string;
		use12HourClock?: boolean;
		pastDisabled?: boolean;
		futureDisabled?: boolean;
	}>(),
	{
		readonly: false,
		pickTime: false,
		pastDisabled: false,
		futureDisabled: false,
		use12HourClock: false,
		format: 'YYYY-MM-DD',
	},
);

const emit = defineEmits(['update:modelValue']);

const weekdaysStrings = [
	__('Mon', 'zionbuilder'),
	__('Tue', 'zionbuilder'),
	__('Wed', 'zionbuilder'),
	__('Thu', 'zionbuilder'),
	__('Fri', 'zionbuilder'),
	__('Sat', 'zionbuilder'),
	__('Sun', 'zionbuilder'),
];

const monthsStrings = [
	__('January', 'zionbuilder'),
	__('February', 'zionbuilder'),
	__('March', 'zionbuilder'),
	__('April', 'zionbuilder'),
	__('May', 'zionbuilder'),
	__('June', 'zionbuilder'),
	__('July', 'zionbuilder'),
	__('August', 'zionbuilder'),
	__('September', 'zionbuilder'),
	__('October', 'zionbuilder'),
	__('November', 'zionbuilder'),
	__('December', 'zionbuilder'),
];

const valueModel = computed({
	get() {
		return props.modelValue;
	},
	set(newValue) {
		/**
		 * It emits the new value
		 */
		emit('update:modelValue', newValue);
	},
});

function disableDate(date) {
	const currentDate = new Date();
	currentDate.setHours(0, 0, 0, 0);

	if (props.pastDisabled) {
		return date < currentDate;
	} else if (props.futureDisabled) {
		return date > currentDate;
	} else return false;
}
</script>
