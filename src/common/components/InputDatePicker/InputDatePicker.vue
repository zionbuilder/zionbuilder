<template>
	<vueDatePick
		v-model="valueModel"
		class="znpb-input-date"
		:next-month-caption="i18n.__('Next Month', 'zionbuilder')"
		:previous-month-caption="i18n.__('Previous month', 'zionbuilder')"
		:set-time-caption="i18n.__('Set time', 'zionbuilder')"
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
import * as i18n from '@wordpress/i18n';
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
	i18n.__('Mon', 'zionbuilder'),
	i18n.__('Tue', 'zionbuilder'),
	i18n.__('Wed', 'zionbuilder'),
	i18n.__('Thu', 'zionbuilder'),
	i18n.__('Fri', 'zionbuilder'),
	i18n.__('Sat', 'zionbuilder'),
	i18n.__('Sun', 'zionbuilder'),
];

const monthsStrings = [
	i18n.__('January', 'zionbuilder'),
	i18n.__('February', 'zionbuilder'),
	i18n.__('March', 'zionbuilder'),
	i18n.__('April', 'zionbuilder'),
	i18n.__('May', 'zionbuilder'),
	i18n.__('June', 'zionbuilder'),
	i18n.__('July', 'zionbuilder'),
	i18n.__('August', 'zionbuilder'),
	i18n.__('September', 'zionbuilder'),
	i18n.__('October', 'zionbuilder'),
	i18n.__('November', 'zionbuilder'),
	i18n.__('December', 'zionbuilder'),
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
