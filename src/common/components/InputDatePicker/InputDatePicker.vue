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

<script>
import { __ } from '@wordpress/i18n';
import vueDatePick from './src/vueDatePick.vue';
import BaseInput from '../BaseInput/BaseInput.vue';

/**
 *   model - string
 */
export default {
	name: 'InputDatePicker',
	components: {
		vueDatePick,
		BaseInput,
	},
	props: {
		/**
		 * Value for input
		 */
		modelValue: {
			type: String,
			required: true,
		},
		readonly: {
			type: Boolean,
			required: false,
		},
		pickTime: {
			type: Boolean,
			required: false,
			default: false,
		},
		format: {
			type: String,
			required: false,
		},
		use12HourClock: {
			type: Boolean,
			required: false,
		},
		pastDisabled: {
			type: Boolean,
			required: false,
		},
		futureDisabled: {
			type: Boolean,
			required: false,
			default: false,
		},
	},
	data() {
		return {
			weekdaysStrings: [
				__('Mon', 'zionbuilder'),
				__('Tue', 'zionbuilder'),
				__('Wed', 'zionbuilder'),
				__('Thu', 'zionbuilder'),
				__('Fri', 'zionbuilder'),
				__('Sat', 'zionbuilder'),
				__('Sun', 'zionbuilder'),
			],
			monthsStrings: [
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
			],
		};
	},
	computed: {
		valueModel: {
			get() {
				return this.modelValue;
			},
			set(newValue) {
				/**
				 * It emits the new value
				 */
				this.$emit('update:modelValue', newValue);
			},
		},
	},
	methods: {
		disableDate(date) {
			const currentDate = new Date();
			currentDate.setHours(0, 0, 0, 0);

			if (this.pastDisabled) {
				return date < currentDate;
			} else if (this.futureDisabled) {
				return date > currentDate;
			} else return false;
		},
	},
};
</script>
<style lang="scss"></style>
