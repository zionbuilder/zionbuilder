<template>
	<div
		class="vdpComponent"
		:class="{vdpWithInput: hasInputElement}"
	>
		<slot
			:open="open"
			:close="close"
			:toggle="toggle"
			:inputValue="inputValue"
			:processUserInput="processUserInput"
			:valueToInputFormat="valueToInputFormat"
		>
			<input
				v-if="hasInputElement"
				type="text"
				v-bind="inputAttributes"
				:readonly="isReadOnly"
				:value="inputValue"
				@input="editable && processUserInput($event.target.value)"
				@focus="editable && open()"
				@click="editable && open()"
			>
			<button
				v-if="editable && hasInputElement && inputValue"
				class="vdpClearInput"
				type="button"
				@click="clear"
			></button>
		</slot>
		<transition name="vdp-toggle-calendar">
			<div
				v-if="opened"
				class="vdpOuterWrap"
				ref="outerWrap"
				@click="closeViaOverlay"
				:class="[positionClass, {vdpFloating: hasInputElement}]"
			>
				<div class="vdpInnerWrap">
					<header class="vdpHeader">
						<button
							class="vdpArrow vdpArrowPrev"
							:title="prevMonthCaption"
							type="button"
							@click="incrementMonth(-1)"
						>{{ prevMonthCaption }}</button>
						<button
							class="vdpArrow vdpArrowNext"
							type="button"
							:title="nextMonthCaption"
							@click="incrementMonth(1)"
						>{{ nextMonthCaption }}</button>
						<div class="vdpPeriodControls">
							<div class="vdpPeriodControl">
								<button
									:class="directionClass"
									:key="currentPeriod.month"
									type="button"
								>
									{{ months[currentPeriod.month] }}
								</button>
								<select v-model="currentPeriod.month">
									<option
										v-for="(month, index) in months"
										:value="index"
										:key="month"
									>
										{{ month }}
									</option>
								</select>
							</div>
							<div class="vdpPeriodControl">
								<button
									:class="directionClass"
									:key="currentPeriod.year"
									type="button"
								>
									{{ currentPeriod.year }}
								</button>
								<select v-model="currentPeriod.year">
									<option
										v-for="year in yearRange"
										:value="year"
										:key="year"
									>
										{{ year }}
									</option>
								</select>
							</div>
						</div>
					</header>
					<table class="vdpTable">
						<thead>
							<tr>
								<th
									class="vdpHeadCell"
									v-for="(weekday, weekdayIndex) in weekdaysSorted"
									:key="weekdayIndex"
								>
									<span class="vdpHeadCellContent">{{weekday}}</span>
								</th>
							</tr>
						</thead>
						<tbody
							:key="currentPeriod.year + '-' + currentPeriod.month"
							:class="directionClass"
						>
							<tr
								class="vdpRow"
								v-for="(week, weekIndex) in currentPeriodDates"
								:key="weekIndex"
							>
								<td
									class="vdpCell"
									v-for="item in week"
									:class="{
                                        selectable: editable && !item.disabled,
                                        selected: item.selected,
                                        disabled: item.disabled,
                                        today: item.today,
                                        outOfRange: item.outOfRange
                                    }"
									:data-id="item.dateKey"
									:key="item.dateKey"
									@click="editable && selectDateItem(item)"
								>
									<div class="vdpCellContent">{{ item.date.getDate() }}</div>
								</td>
							</tr>
						</tbody>
					</table>
					<div
						v-if="pickTime && currentTime"
						class="vdpTimeControls"
					>
						<span class="vdpTimeCaption">{{ setTimeCaption }}</span>
						<div class="vdpTimeUnit">
							<pre><span>{{ currentTime.hoursFormatted }}</span><br></pre>
							<input
								type="number"
								pattern="\d*"
								class="vdpHoursInput"
								@input.prevent="inputHours"
								@focusin="onTimeInputFocus"
								:disabled="!editable"
								:value="currentTime.hoursFormatted"
							>
						</div>
						<span
							v-if="pickMinutes"
							class="vdpTimeSeparator"
						>:</span>
						<div
							v-if="pickMinutes"
							class="vdpTimeUnit"
						>
							<pre><span>{{ currentTime.minutesFormatted }}</span><br></pre>
							<input
								v-if="pickMinutes"
								type="number"
								pattern="\d*"
								class="vdpMinutesInput"
								@input="inputTime('setMinutes', $event)"
								@focusin="onTimeInputFocus"
								:disabled="!editable"
								:value="currentTime.minutesFormatted"
							>
						</div>
						<span
							v-if="pickSeconds"
							class="vdpTimeSeparator"
						>:</span>
						<div
							v-if="pickSeconds"
							class="vdpTimeUnit"
						>
							<pre><span>{{ currentTime.secondsFormatted }}</span><br></pre>
							<input
								v-if="pickSeconds"
								type="number"
								pattern="\d*"
								class="vdpSecondsInput"
								@input="inputTime('setSeconds', $event)"
								@focusin="onTimeInputFocus"
								:disabled="!editable"
								:value="currentTime.secondsFormatted"
							>
						</div>
						<button
							v-if="use12HourClock"
							type="button"
							class="vdp12HourToggleBtn"
							:disabled="!editable"
							@click="set12HourClock(currentTime.isPM ? 'AM' : 'PM')"
						>
							{{ currentTime.isPM ? 'PM' : 'AM' }}
						</button>
					</div>
				</div>
			</div>
		</transition>
	</div>
</template>

<script>

const formatRE = /,|\.|-| |:|\/|\\/;
const dayRE = /D+/;
const monthRE = /M+/;
const yearRE = /Y+/;
const hoursRE = /h+/i;
const minutesRE = /m+/;
const secondsRE = /s+/;
const AMPMClockRE = /A/;

export default {

	props: {
		modelValue: {
			type: String,
			default: ''
		},
		format: {
			type: String,
			default: 'YYYY-MM-DD'
		},
		displayFormat: {
			type: String
		},
		editable: {
			type: Boolean,
			default: true
		},
		hasInputElement: {
			type: Boolean,
			default: true
		},
		inputAttributes: {
			type: Object
		},
		selectableYearRange: {
			type: [Number, Object, Function],
			default: 40
		},
		startPeriod: {
			type: Object
		},
		parseDate: {
			type: Function
		},
		formatDate: {
			type: Function
		},
		pickTime: {
			type: Boolean,
			default: false
		},
		pickMinutes: {
			type: Boolean,
			default: true
		},
		pickSeconds: {
			type: Boolean,
			default: false
		},
		use12HourClock: {
			type: Boolean,
			default: false
		},
		isDateDisabled: {
			type: Function,
			default: () => false
		},
		nextMonthCaption: {
			type: String,
			default: 'Next month'
		},
		prevMonthCaption: {
			type: String,
			default: 'Previous month'
		},
		setTimeCaption: {
			type: String,
			default: 'Set time:'
		},
		mobileBreakpointWidth: {
			type: Number,
			default: 500
		},
		weekdays: {
			type: Array,
			default: () => ([
				'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
			])
		},
		months: {
			type: Array,
			default: () => ([
				'January', 'February', 'March', 'April',
				'May', 'June', 'July', 'August',
				'September', 'October', 'November', 'December'
			])
		},
		startWeekOnSunday: {
			type: Boolean,
			default: false
		}
	},

	data () {
		return {
			inputValue: this.valueToInputFormat(this.modelValue),
			direction: undefined,
			positionClass: undefined,
			opened: !this.hasInputElement,
			currentPeriod: this.startPeriod || this.getPeriodFromValue(
				this.modelValue, this.format
			)
		};
	},

	computed: {

		valueDate () {

			const value = this.modelValue;
			const format = this.format;

			return value
				? this.parseDateString(value, format)
				: undefined
				;

		},

		isReadOnly () {
			return !this.editable || (this.inputAttributes && this.inputAttributes.readonly);
		},

		isValidValue () {

			const valueDate = this.valueDate;

			return this.modelValue ? Boolean(valueDate) : true;

		},

		currentPeriodDates () {

			const { year, month } = this.currentPeriod;
			const days = [];
			const date = new Date(year, month, 1);
			const today = new Date();
			const offset = this.startWeekOnSunday ? 1 : 0;

			// append prev month dates
			const startDay = date.getDay() || 7;

			if (startDay > (1 - offset)) {
				for (let i = startDay - (2 - offset); i >= 0; i--) {

					const prevDate = new Date(date);
					prevDate.setDate(-i);
					days.push({ outOfRange: true, date: prevDate });

				}
			}

			while (date.getMonth() === month) {
				days.push({ date: new Date(date) });
				date.setDate(date.getDate() + 1);
			}

			// append next month dates
			const daysLeft = 7 - days.length % 7;

			for (let i = 1; i <= daysLeft; i++) {

				const nextDate = new Date(date);
				nextDate.setDate(i);
				days.push({ outOfRange: true, date: nextDate });

			}

			// define day states
			days.forEach(day => {
				day.disabled = this.isDateDisabled(day.date);
				day.today = areSameDates(day.date, today);
				day.dateKey = [
					day.date.getFullYear(), day.date.getMonth() + 1, day.date.getDate()
				].join('-');
				day.selected = this.valueDate ? areSameDates(day.date, this.valueDate) : false;
			});

			return chunkArray(days, 7);

		},

		yearRange () {

			const currentYear = this.currentPeriod.year;
			const userRange = this.selectableYearRange;
			const userRangeType = typeof userRange;

			let yearsRange = [];

			if (userRangeType === 'number') {
				yearsRange = range(
					currentYear - userRange,
					currentYear + userRange
				);
			} else if (userRangeType === 'object') {
				yearsRange = range(
					userRange.from,
					userRange.to
				);
			} else if (userRangeType === 'function') {
				yearsRange = userRange(this);
			}

			if (yearsRange.indexOf(currentYear) < 0) {
				yearsRange.push(currentYear);
				yearsRange = yearsRange.sort();
			}

			return yearsRange;

		},

		currentTime () {

			const currentDate = this.valueDate;

			if (!currentDate) {
				return undefined;
			}

			const hours = currentDate.getHours();
			const minutes = currentDate.getMinutes();
			const seconds = currentDate.getSeconds();

			return {
				hours,
				minutes,
				seconds,
				isPM: isPM(hours),
				hoursFormatted: (this.use12HourClock ? to12HourClock(hours) : hours).toString(),
				minutesFormatted: paddNum(minutes, 2),
				secondsFormatted: paddNum(seconds, 2)
			};

		},

		directionClass () {

			return this.direction ? `vdp${this.direction}Direction` : undefined;

		},

		weekdaysSorted () {

			if (this.startWeekOnSunday) {
				const weekdays = this.weekdays.slice();
				weekdays.unshift(weekdays.pop());
				return weekdays;
			} else {
				return this.weekdays;
			}

		}

	},

	watch: {

		modelValue (value) {

			if (this.isValidValue) {
				this.inputValue = this.valueToInputFormat(value);
				this.currentPeriod = this.getPeriodFromValue(value, this.format);
			}

		},

		currentPeriod (currentPeriod, oldPeriod) {

			const currentDate = new Date(currentPeriod.year, currentPeriod.month).getTime();
			const oldDate = new Date(oldPeriod.year, oldPeriod.month).getTime();

			this.direction = currentDate !== oldDate
				? (currentDate > oldDate ? 'Next' : 'Prev')
				: undefined
				;

			if (currentDate !== oldDate) {
				this.$emit('periodChange', {
					year: currentPeriod.year,
					month: currentPeriod.month
				});
			}

		}

	},

	beforeUnmount () {

		this.removeCloseEvents();
		this.teardownPosition();

	},

	methods: {

		valueToInputFormat (value) {

			return !this.displayFormat ? value : this.formatDateToString(
				this.parseDateString(value, this.format), this.displayFormat
			) || value;

		},

		getPeriodFromValue (dateString, format) {

			const date = this.parseDateString(dateString, format) || new Date();

			return { month: date.getMonth(), year: date.getFullYear() };

		},

		parseDateString (dateString, dateFormat) {

			return !dateString
				? undefined
				: this.parseDate
					? this.parseDate(dateString, dateFormat)
					: this.parseSimpleDateString(dateString, dateFormat)
				;

		},

		formatDateToString (date, dateFormat) {

			return !date
				? ''
				: this.formatDate
					? this.formatDate(date, dateFormat)
					: this.formatSimpleDateToString(date, dateFormat)
				;

		},

		parseSimpleDateString (dateString, dateFormat) {

			let day, month, year, hours, minutes, seconds;

			const dateParts = dateString.split(formatRE);
			const formatParts = dateFormat.split(formatRE);
			const partsSize = formatParts.length;

			for (let i = 0; i < partsSize; i++) {

				if (formatParts[i].match(dayRE)) {
					day = parseInt(dateParts[i], 10);
				} else if (formatParts[i].match(monthRE)) {
					month = parseInt(dateParts[i], 10);
				} else if (formatParts[i].match(yearRE)) {
					year = parseInt(dateParts[i], 10);
				} else if (formatParts[i].match(hoursRE)) {
					hours = parseInt(dateParts[i], 10);
				} else if (formatParts[i].match(minutesRE)) {
					minutes = parseInt(dateParts[i], 10);
				} else if (formatParts[i].match(secondsRE)) {
					seconds = parseInt(dateParts[i], 10);
				}

			};

			const resolvedDate = new Date(
				[paddNum(year, 4), paddNum(month, 2), paddNum(day, 2)].join('-')
			);

			if (isNaN(resolvedDate)) {
				return undefined;
			} else {

				const date = new Date(year, month - 1, day);

				[
					[year, 'setFullYear'],
					[hours, 'setHours'],
					[minutes, 'setMinutes'],
					[seconds, 'setSeconds']
				].forEach(([value, method]) => {
					typeof value !== 'undefined' && date[method](value);
				});

				return date;
			}

		},

		formatSimpleDateToString (date, dateFormat) {

			return dateFormat
				.replace(yearRE, match => Number(date.getFullYear().toString().slice(-match.length)))
				.replace(monthRE, match => paddNum(date.getMonth() + 1, match.length))
				.replace(dayRE, match => paddNum(date.getDate(), match.length))
				.replace(hoursRE, match => paddNum(
					AMPMClockRE.test(dateFormat) ? to12HourClock(date.getHours()) : date.getHours(),
					match.length
				))
				.replace(minutesRE, match => paddNum(date.getMinutes(), match.length))
				.replace(secondsRE, match => paddNum(date.getSeconds(), match.length))
				.replace(AMPMClockRE, match => isPM(date.getHours()) ? 'PM' : 'AM')
				;

		},

		incrementMonth (increment = 1) {

			const refDate = new Date(this.currentPeriod.year, this.currentPeriod.month);
			const incrementDate = new Date(refDate.getFullYear(), refDate.getMonth() + increment);

			this.currentPeriod = {
				month: incrementDate.getMonth(),
				year: incrementDate.getFullYear()
			};

		},

		processUserInput (userText) {

			const userDate = this.parseDateString(
				userText, this.displayFormat || this.format
			);

			this.inputValue = userText;

			this.$emit('update:modelValue', userDate
				? this.formatDateToString(userDate, this.format)
				: userText
			);

		},

		toggle () {

			return this.opened ? this.close() : this.open();

		},

		open () {

			if (!this.opened) {
				this.opened = true;
				this.currentPeriod = this.startPeriod || this.getPeriodFromValue(
					this.modelValue, this.format
				);
				this.addCloseEvents();
				this.setupPosition();
			}
			this.direction = undefined;

		},

		close () {

			if (this.opened) {
				this.opened = false;
				this.direction = undefined;
				this.removeCloseEvents();
				this.teardownPosition();
			}

		},

		closeViaOverlay (e) {

			if (this.hasInputElement && e.target === this.$refs.outerWrap) {
				this.close();
			}

		},

		addCloseEvents () {

			if (!this.closeEventListener) {

				this.closeEventListener = e => this.inspectCloseEvent(e);

				['click', 'keyup', 'focusin'].forEach(
					eventName => document.addEventListener(eventName, this.closeEventListener)
				);

			}

		},

		inspectCloseEvent (event) {

			if (event.keyCode) {
				event.keyCode === 27 && this.close();
			} else if (!(event.target === this.$el) && !this.$el.contains(event.target)) {
				this.close();
			}

		},

		removeCloseEvents () {

			if (this.closeEventListener) {

				['click', 'keyup', 'focusin'].forEach(
					eventName => document.removeEventListener(eventName, this.closeEventListener)
				);

				delete this.closeEventListener;

			}

		},

		setupPosition () {

			if (!this.positionEventListener) {
				this.positionEventListener = () => this.positionFloater();
				window.addEventListener('resize', this.positionEventListener);
			}

			this.positionFloater();

		},

		positionFloater () {

			const inputRect = this.$el.getBoundingClientRect();

			let verticalClass = 'vdpPositionTop';
			let horizontalClass = 'vdpPositionLeft';

			const calculate = () => {

				const rect = this.$refs.outerWrap.getBoundingClientRect();
				const floaterHeight = rect.height;
				const floaterWidth = rect.width;

				if (window.innerWidth > this.mobileBreakpointWidth) {

					// vertical
					if (
						(inputRect.top + inputRect.height + floaterHeight > window.innerHeight) &&
						(inputRect.top - floaterHeight > 0)
					) {
						verticalClass = 'vdpPositionBottom';
					}

					// horizontal
					if (inputRect.left + floaterWidth > window.innerWidth) {
						horizontalClass = 'vdpPositionRight';
					}

					this.positionClass = ['vdpPositionReady', verticalClass, horizontalClass].join(' ');

				} else {

					this.positionClass = 'vdpPositionFixed';

				}

			};

			this.$refs.outerWrap ? calculate() : this.$nextTick(calculate);

		},

		teardownPosition () {

			if (this.positionEventListener) {
				this.positionClass = undefined;
				window.removeEventListener('resize', this.positionEventListener);
				delete this.positionEventListener;
			}

		},

		clear () {

			this.$emit('update:modelValue', '');

		},

		selectDateItem (item) {

			if (!item.disabled) {

				const newDate = new Date(item.date);

				if (this.currentTime) {
					newDate.setHours(this.currentTime.hours);
					newDate.setMinutes(this.currentTime.minutes);
					newDate.setSeconds(this.currentTime.seconds);
				}

				this.$emit('update:modelValue', this.formatDateToString(newDate, this.format));

				if (this.hasInputElement && !this.pickTime) {
					this.close();
				}
			}

		},

		set12HourClock (value) {

			const currentDate = new Date(this.valueDate);
			const currentHours = currentDate.getHours();

			currentDate.setHours(value === 'PM'
				? currentHours + 12
				: currentHours - 12
			);

			this.$emit('update:modelValue', this.formatDateToString(currentDate, this.format));
		},

		inputHours (event) {

			const currentDate = new Date(this.valueDate);
			const currentHours = currentDate.getHours();
			const targetValue = parseInt(event.target.value, 10) || 0;
			const minHours = this.use12HourClock ? 1 : 0;
			const maxHours = this.use12HourClock ? 12 : 23;
			const numValue = boundNumber(targetValue, minHours, maxHours);

			currentDate.setHours(this.use12HourClock
				? to24HourClock(numValue, isPM(currentHours))
				: numValue
			);
			event.target.value = paddNum(numValue, 1);
			this.$emit('update:modelValue', this.formatDateToString(currentDate, this.format));

		},

		inputTime (method, event) {

			const currentDate = new Date(this.valueDate);
			const targetValue = parseInt(event.target.value) || 0;
			const numValue = boundNumber(targetValue, 0, 59);

			event.target.value = paddNum(numValue, 2);
			currentDate[method](numValue);

			this.$emit('update:modelValue', this.formatDateToString(currentDate, this.format));

		},

		onTimeInputFocus (event) {
			event.target.select && event.target.select();
		}

	}

};

function paddNum (num, padsize) {

	return typeof num !== 'undefined'
		? num.toString().length > padsize
			? num
			: new Array(padsize - num.toString().length + 1).join('0') + num
		: undefined
		;

}

function chunkArray (inputArray, chunkSize) {

	const results = [];

	while (inputArray.length) {
		results.push(inputArray.splice(0, chunkSize));
	}

	return results;

}

function areSameDates (date1, date2) {

	return (date1.getDate() === date2.getDate()) &&
		(date1.getMonth() === date2.getMonth()) &&
		(date1.getFullYear() === date2.getFullYear())
		;

}

function range (start, end) {

	const results = [];

	for (let i = start; i <= end; i++) {
		results.push(i);
	}
	return results;

}

function to12HourClock (hours) {

	const remainder = hours % 12;
	return remainder === 0 ? 12 : remainder;

}

function to24HourClock (hours, PM) {

	return PM
		? (hours === 12 ? hours : hours + 12)
		: (hours === 12 ? 0 : hours)
		;

}

function isPM (hours) {

	return hours >= 12;

}

function boundNumber (value, min, max) {
	return Math.min(Math.max(value, min), max);
}

</script>

<style lang="scss">
$vdpColor: $secondary;

@keyframes vdpSlideFromLeft {
	from {
		transform: translate3d(-.5em, 0, 0);
		opacity: 0;
	}
	to {
		transform: translate3d(0, 0, 0);
		opacity: 1;
	}
}

@keyframes vdpSlideFromRight {
	from {
		transform: translate3d(.5em, 0, 0);
		opacity: 0;
	}
	to {
		transform: translate3d(0, 0, 0);
		opacity: 1;
	}
}

@keyframes vdpToggleCalendar {
	from {
		transform: scale(.5);
		opacity: 0;
	}
	to {
		transform: scale(1);
		opacity: 1;
	}
}

@keyframes vdpFadeCalendar {
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}

.vdp-toggle-calendar-enter-active.vdpPositionReady {
	transform-origin: top left;
	animation: vdpToggleCalendar .2s;
}

.vdp-toggle-calendar-leave-active {
	animation: vdpToggleCalendar .15s reverse;
}

.vdp-toggle-calendar-enter-active.vdpPositionFixed {
	animation: vdpFadeCalendar .3s;
}

.vdp-toggle-calendar-leave-active.vdpPositionFixed {
	animation: vdpFadeCalendar .3s reverse;
}

.vdpComponent {
	position: relative;
	display: inline-block;
	color: #303030;
	font-size: 10px;
	/*font-family: Helvetica, Arial, sans-serif;*/
}

.vdpComponent.vdpWithInput > input {
	padding-right: 30px;
}

.vdpClearInput {
	position: absolute;
	top: 0;
	right: 0;
	bottom: 0;
	width: 3em;
	font-size: 1em;
}

.vdpClearInput:before {
	content: "×";
	position: absolute;
	top: 50%;
	left: 50%;
	box-sizing: border-box;
	width: 1.4em;
	height: 1.4em;
	margin: -.7em 0 0 -.7em;
	color: rgba(0, 0, 0, .3);
	line-height: 1.1em;
	background-color: #fff;
	border: 1px solid rgba(0, 0, 0, .15);
	border-radius: 50%;
}

.vdpClearInput:hover:before {
	box-shadow: 0 .2em .5em rgba(0, 0, 0, .15);
}

.vdpOuterWrap.vdpFloating {
	position: absolute;
	z-index: 220;
	padding: .5em 0;
}

.vdpOuterWrap.vdpPositionFixed {
	position: fixed;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 2em;
	background-color: rgba(0, 0, 0, .3);
}

.vdpFloating .vdpInnerWrap {
	max-width: 30em;
}

.vdpPositionFixed .vdpInnerWrap {
	max-width: 30em;
	margin: 0 auto;
	border: 0;
	animation: vdpToggleCalendar .3s;
}

.vdpFloating.vdpPositionTop {
	top: 100%;
}
.vdpFloating.vdpPositionBottom {
	bottom: 100%;
}
.vdpFloating.vdpPositionLeft {
	left: 0;
}
.vdpFloating.vdpPositionRight {
	right: 0;
}

.vdpPositionTop.vdpPositionLeft {
	transform-origin: top left;
}
.vdpPositionTop.vdpPositionRight {
	transform-origin: top right;
}
.vdpPositionBottom.vdpPositionLeft {
	transform-origin: bottom left;
}
.vdpPositionBottom.vdpPositionRight {
	transform-origin: bottom right;
}

.vdpInnerWrap {
	box-sizing: border-box;
	overflow: hidden;
	min-width: 28em;
	padding: 1em;
	background: #fff;
	box-shadow: 0 .2em 1.5em rgba(0, 0, 0, .06);
	border: 1px solid rgba(0, 0, 0, .15);
	border-radius: .5em;
}

.vdpHeader {
	position: relative;
	padding: 0 1em 2.5em;
	margin: -1em -1em -2.5em;
	text-align: center;
	background: #f5f5f5;
}

.vdpClearInput, .vdpArrow, .vdpPeriodControl > button, .vdp12HourToggleBtn {
	padding: 0;
	margin: 0;
	background: none;
	border: 0;
	cursor: pointer;
}

.vdpArrow::-moz-focus-inner, .vdpClearInput::-moz-focus-inner, .vdpPeriodControl::-moz-focus-inner, .vdp12HourToggleBtn::-moz-focus-inner {
	padding: 0;
	border: 0;
}

.vdpArrow {
	position: absolute;
	top: 0;
	bottom: 2.5em;
	overflow: hidden;
	width: 5em;
	font-size: 1em;
	text-align: left;
	text-indent: -999em;
}

.vdpArrow:before {
	content: "";
	position: absolute;
	top: 50%;
	left: 50%;
	width: 2.2em;
	height: 2.2em;
	margin: -1.1em 0 0 -1.1em;
	border-radius: 100%;
	transition: background-color .2s;
}

.vdpArrow:hover, .vdpArrow:focus, .vdpArrow:active {
	outline: 0;
}

.vdpArrow:hover:before, .vdpArrow:focus:before {
	background-color: rgba(0, 0, 0, .03);
}

.vdpArrow:active:before {
	background-color: rgba(0, 0, 0, .07);
}

.vdpArrowNext:before {
	margin-left: -1.4em;
}

.vdpArrow:after {
	content: "";
	position: absolute;
	top: 50%;
	left: 50%;
	width: 0;
	height: 0;
	margin-top: -.5em;
	border: .5em solid transparent;
}

.vdpArrowPrev {
	left: -.3em;
}

.vdpArrowPrev:after {
	margin-left: -.8em;
	border-right-color: $vdpColor;
}

.vdpArrowNext {
	right: -.6em;
}

.vdpArrowNext:after {
	margin-left: -.5em;
	border-left-color: $vdpColor;
}

.vdpPeriodControl {
	position: relative;
	display: inline-block;
}

.vdpPeriodControl > button {
	display: inline-block;
	padding: 1em .4em;
	font-size: 1.5em;
}

.vdpPeriodControl > select {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	font-size: 1.6em;
	cursor: pointer;
	opacity: 0;
}

.vdpTable {
	position: relative;
	z-index: 5;
	width: 100%;
	table-layout: fixed;
}

.vdpNextDirection {
	animation: vdpSlideFromRight .5s;
}

.vdpPrevDirection {
	animation: vdpSlideFromLeft .5s;
}

.vdpCell, .vdpHeadCell {
	box-sizing: border-box;
	text-align: center;
}

.vdpCell {
	padding: .5em 0;
}

.vdpHeadCell {
	padding: .3em .5em 1.8em;
}

.vdpHeadCellContent {
	color: #848484;
	font-size: 1.3em;
	font-weight: normal;
}

.vdpCellContent {
	display: block;
	width: 1.857em;
	margin: 0 auto;
	font-size: 1.4em;
	line-height: 1.857em;
	text-align: center;
	border-radius: 100%;
	transition: background .1s, color .1s;
}

.vdpCell.outOfRange {
	color: #c7c7c7;
}

.vdpCell.today {
	color: $vdpColor;
}

.vdpCell.selected .vdpCellContent {
	color: #fff;
	background: $vdpColor;
}

@media (hover: hover) {
	.vdpCell.selectable:hover .vdpCellContent {
		color: #fff;
		background: $vdpColor;
	}
}

.vdpCell.selectable {
	cursor: pointer;
}

.vdpCell.disabled {
	opacity: .5;
}

.vdpTimeControls {
	position: relative;
	padding: 1.2em 2em;
	margin: 1em -1em -1em;
	text-align: center;
	background: #f5f5f5;
	/*border-top: 1px solid rgba(0,0,0,0.15);*/
}

.vdpTimeUnit {
	position: relative;
	display: inline-block;
	vertical-align: middle;
}

.vdpTimeUnit > pre, .vdpTimeUnit > input {
	box-sizing: border-box;
	padding: .1em .1em;
	margin: 0;
	color: #000;
	font-size: 1.7em;
	line-height: 1.3;
	text-align: center;
	word-wrap: break-word;
	white-space: pre-wrap;
	border: 0;
	border-bottom: 1px solid transparent;
	resize: none;
}

.vdpTimeUnit > pre {
	font-family: inherit;
	visibility: hidden;
}

.vdpTimeUnit > input {
	position: absolute;
	top: 0;
	left: 0;
	overflow: hidden;
	width: 100%;
	height: 100%;
	padding: 0;
	        appearance: none;
	        appearance: textfield;
	background: transparent;
	border-radius: 0;
	outline: none;

	-webkit-appearance: textfield;
	   -moz-appearance: textfield;

	&::selection {
		background-color: rgba($vdpColor, .15);
	}
}

.vdpTimeUnit > input:hover, .vdpTimeUnit > input:focus {
	border-bottom-color: $vdpColor;
}

.vdpTimeUnit > input:disabled {
	border-bottom-color: transparent;
}

.vdpTimeUnit > input::-webkit-inner-spin-button, .vdpTimeUnit > input::-webkit-outer-spin-button {
	margin: 0;

	-webkit-appearance: none;
}

.vdpTimeSeparator, .vdpTimeCaption {
	display: inline-block;
	color: #848484;
	font-size: 1.3em;
	vertical-align: middle;
}

.vdpTimeCaption {
	margin-right: .5em;
}

.vdp12HourToggleBtn {
	display: inline-block;
	padding: 0 .4em;
	color: #303030;
	font-size: 1.3em;
	vertical-align: middle;
}

.vdp12HourToggleBtn:hover, .vdp12HourToggleBtn:focus {
	color: $vdpColor;
	outline: 0;
}

.vdp12HourToggleBtn:disabled {
	color: #303030;
}
</style>
