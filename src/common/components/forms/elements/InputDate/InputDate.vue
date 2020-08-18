<template>

	<date-pick
		v-model="valueModel"
		class="znpb-input-date"
		:next-month-caption="$translate('next_month')"
		:previous-month-caption="$translate('previous_month')"
		:set-time-caption="$translate('set_time')"
		:weekdays="weekdaysStrings"
		:months="monthsStrings"
		:pick-time="pickTime"
		:use-12-hour-clock="use12HourClock"
		:format="format"
		:is-date-disabled="disableDate"
	>

		<template v-slot:default="{toggle}">
			<BaseInput
				v-model="valueModel"
				:readonly="readonly"
				class="znpb-input-number__input"
				v-bind="$attrs"
				@keydown.native="toggle"
				@mouseup.native="toggle"
			>
			</BaseInput>
		</template>
	</date-pick>

</template>

<script>
import DatePick from 'vue-date-pick'
import BaseInput from '../input/BaseInput'

/**
 *   model - string
 */
export default {
	name: 'InputDate',
	components: {
		DatePick,
		BaseInput
	},
	props: {
		/**
		 * Value for input
		 */
		value: {
			type: String,
			required: true
		},
		readonly: {
			type: Boolean,
			required: false
		},
		pickTime: {
			type: Boolean,
			required: false,
			default: false
		},
		format: {
			type: String,
			required: false
		},
		use12HourClock: {
			type: Boolean,
			required: false
		},
		pastDisabled: {
			type: Boolean,
			required: false
		},
		futureDisabled: {
			type: Boolean,
			required: false,
			default: false
		}
	},
	data () {
		return {
			weekdaysStrings: [
				this.$translate('monday'),
				this.$translate('tuesday'),
				this.$translate('wednesday'),
				this.$translate('thursday'),
				this.$translate('friday'),
				this.$translate('saturday'),
				this.$translate('sunday')
			],
			monthsStrings: [
				this.$translate('jan'),
				this.$translate('feb'),
				this.$translate('mar'),
				this.$translate('apr'),
				this.$translate('may'),
				this.$translate('jun'),
				this.$translate('jul'),
				this.$translate('aug'),
				this.$translate('sep'),
				this.$translate('oct'),
				this.$translate('nov'),
				this.$translate('dec')
			]
		}
	},
	computed: {
		valueModel: {
			get () {
				return this.value
			},
			set (newValue) {
				/**
				 * It emits the new value
				*/
				this.$emit('input', newValue)
			}
		}
	},
	methods: {
		disableDate (date) {
			const currentDate = new Date()
			currentDate.setHours(0, 0, 0, 0)

			if (this.pastDisabled) {
				return date < currentDate
			} else if (this.futureDisabled) {
				return date > currentDate
			} else return false
		}

	}
}
</script>
<style lang="scss">
$vdpColor: $secondary;
@import "vue-date-pick/src/vueDatePick.scss";
</style>
