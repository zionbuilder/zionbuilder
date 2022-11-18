<template>
	<div class="znpb-input-number-unit">
		<BaseInput
			ref="numberUnitInput"
			:model-value="localRawValue"
			class="znpb-input-number--has-units"
			size="narrow"
			:placeholder="placeholder"
			:disabled="isValueDisabled"
			@update:model-value="onTextValueChange"
			@mousedown.stop="actNumberDrag"
			@touchstart.prevent.passive="actNumberDrag"
			@mouseup="deactivateDragNumber"
			@keydown="onKeyDown"
		>
			<template #suffix>
				<Tooltip
					v-model:show="showUnits"
					trigger="click"
					placement="bottom"
					append-to="element"
					:show-arrows="false"
					strategy="fixed"
					tooltip-class="hg-popper--no-padding"
					:close-on-outside-click="true"
					class="znpb-input-number__units-tooltip-wrapper"
					:class="{
						'znpb-input-number__isValueDisabled': isValueDisabled,
					}"
				>
					<template #content>
						<div class="znpb-number-unit-list hg-popper-list">
							<div
								v-for="(unit, i) in units"
								:key="i"
								class="znpb-number-unit-list__option hg-popper-list__item"
								:class="{
									[`znpb-number-unit-list__option--selected`]: localUnit === unit,
								}"
								@click.stop="changeUnit(unit)"
							>
								{{ unit.length ? unit : '-' }}
							</div>

							<div
								class="znpb-number-unit-list__option hg-popper-list__item"
								:class="{
									[`znpb-number-unit-list__option--selected`]: localUnit === '',
								}"
								@click.stop="changeUnit('')"
							>
								{{ translate('custom') }}
							</div>
						</div>
					</template>

					<span class="znpb-input-number__unitValue">{{ localUnit.length ? localUnit : '-' }}</span>
				</Tooltip>
			</template>
		</BaseInput>
	</div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, onMounted, ref, watch, Ref } from 'vue';
import rafSchd from 'raf-schd';

import { DEFAULT_UNIT_TYPES, UNITS_WITHOUT_VALUE } from '../../data';
import Tooltip from '../tooltip/components/Tooltip.vue';
import BaseInput from '../BaseInput/BaseInput.vue';
import { translate } from '../../modules/i18n';

const props = withDefaults(
	defineProps<{
		modelValue?: string;
		min?: number;
		max?: number;
		step?: number;
		// eslint-disable-next-line vue/prop-name-casing
		shift_step?: number;
		placeholder?: string;
		// eslint-disable-next-line vue/prop-name-casing
		default_unit?: string;
		units?: string[];
	}>(),
	{
		modelValue: '',
		min: -Infinity,
		max: Infinity,
		step: 1,
		shift_step: 5,
		placeholder: '',
		default_unit: '',
		units: function () {
			return DEFAULT_UNIT_TYPES;
		},
	},
);

const emit = defineEmits(['update:modelValue', 'linked-value']);

const localRawValue = ref('');
const localValue = ref(0);
const localUnit: Ref<string> = ref('');
const showUnits = ref(false);

watch(
	() => props.modelValue,
	newValue => {
		const { unit, value, rawValue } = getValuesFromString(newValue);
		localRawValue.value = rawValue;
		localValue.value = null !== value ? value : getValueInRange(0);

		if (localRawValue.value.length) {
			if (UNITS_WITHOUT_VALUE.includes(localRawValue.value)) {
				localUnit.value = localRawValue.value;
				localRawValue.value = '';
			} else {
				localUnit.value = unit ?? '';
			}
		} else {
			// Use default unit
			localUnit.value = props.units[0];
		}
	},
	{
		immediate: true,
	},
);

const isValueDisabled = computed(() => {
	return UNITS_WITHOUT_VALUE.includes(localUnit.value);
});

function isNumeric(value: string) {
	return !isNaN(value) && !isNaN(parseFloat(value));
}

function isValidUnit(unit: string) {
	return props.units.includes(unit);
}

function getValuesFromString(string: string) {
	let unit = null;
	let value = null;
	let rawValue = '';

	if (isNumeric(string)) {
		value = parseFloat(string);
		rawValue = string;
	} else {
		const { value: parsedValue, unit: parsedUnit } = getIntegerAndUnit(string);

		const unitIsValid = parsedUnit !== null && isValidUnit(parsedUnit);

		if (parsedValue !== null && parsedUnit !== null) {
			rawValue = `${parsedValue}`;
			value = parsedValue;
			unit = unitIsValid ? parsedUnit : '';
		} else {
			rawValue = string;
			unit = unitIsValid ? parsedUnit : '';
		}
	}

	return {
		unit,
		value,
		rawValue,
	};
}

function changeUnit(newUnit: string) {
	showUnits.value = false;
	localUnit.value = newUnit;

	if (localValue.value) {
		if (UNITS_WITHOUT_VALUE.includes(newUnit)) {
			onTextValueChange(newUnit);
		} else {
			onTextValueChange(`${localValue.value}${newUnit}`);
		}
	}
}

function onTextValueChange(newValue: string) {
	const { unit, value, rawValue } = getValuesFromString(newValue);
	const validUnit = unit ? unit : localUnit.value;

	// Ensure that the input gets updated..
	localRawValue.value = newValue;

	if (value !== null && validUnit) {
		const validValue = getValueInRange(value);
		emit('update:modelValue', `${validValue}${validUnit}`);
	} else {
		emit('update:modelValue', rawValue);
	}
}

function getValueInRange(value: number) {
	return Math.max(props.min, Math.min(props.max, value));
}

function getIntegerAndUnit(string: string) {
	const match =
		typeof string === 'string' && string ? string.match(/^([+-]?[0-9]+([.][0-9]*)?|[.][0-9]+)(\D+)?$/) : null;
	const value = match && match[1] ? parseInt(match[1]) : null;
	const unit = match && match[3] ? match[3] : null;
	return {
		value,
		unit,
	};
}

let mouseDownPositionTop = 0;
let draggingPositionTop = 0;
let dragThreshold = 3;
let shiftDrag = false;
let toTop = false;
let directionReset = 0;
let draggingCached = 0;
let dragging = false;
const dragNumberThrottle = rafSchd(dragNumber);

function actNumberDrag(event: MouseEvent) {
	dragging = true;
	draggingCached = localValue.value;
	mouseDownPositionTop = event.clientY;

	// Don't proceed if we do not have a valid unit
	if (!canUpdateNumber()) {
		return;
	}

	document.body.style.userSelect = 'none';
	window.addEventListener('mousemove', dragNumberThrottle);
	window.addEventListener('mouseup', deactivateDragNumber);
}

function canUpdateNumber() {
	return localRawValue.value === '' || !UNITS_WITHOUT_VALUE.includes(localUnit.value);
}

function onKeyDown(event: KeyboardEvent) {
	if (event.altKey) {
		event.preventDefault();
		emit('linked-value');
	}

	shiftDrag = event.shiftKey;

	// Check if the value has a valid unit
	if (!canUpdateNumber()) {
		return;
	}

	// Check if up/down arrow was presses
	if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
		toTop = event.key === 'ArrowUp';
		setDraggingValue();
		event.preventDefault();
	}
}

function deactivateDragNumber() {
	dragNumberThrottle.cancel();
	dragging = false;
	document.body.style.userSelect = '';
	document.body.style.pointerEvents = '';
	window.removeEventListener('mousemove', dragNumberThrottle);
}
function removeEvents() {
	deactivateDragNumber();
	window.removeEventListener('mouseup', deactivateDragNumber);
}

function dragNumber(event: MouseEvent) {
	const pageY = event.pageY;
	shiftDrag = event.shiftKey;
	draggingPositionTop = event.clientY;

	if (Math.abs(mouseDownPositionTop - draggingPositionTop) > dragThreshold) {
		if (pageY < directionReset) {
			toTop = true;
		} else {
			toTop = false;
		}

		document.body.style.pointerEvents = 'none';

		if (pageY !== directionReset) {
			setDraggingValue();
		}
	}

	directionReset = event.pageY;
}

function setDraggingValue() {
	let newValue;
	if (dragging) {
		const dragged = mouseDownPositionTop - dragThreshold - draggingPositionTop;
		newValue = draggingCached + dragged;

		if (shiftDrag) {
			newValue = newValue % props.shift_step ? Math.ceil(newValue / props.shift_step) * props.shift_step : newValue;
		}
	} else {
		let increment = 1;
		if (shiftDrag) {
			increment = toTop ? props.shift_step : -props.shift_step;
		} else {
			increment = toTop ? props.step : -props.step;
		}

		newValue = localValue.value + increment;
		if (shiftDrag) {
			newValue = newValue % props.shift_step ? Math.ceil(newValue / props.shift_step) * props.shift_step : newValue;
			if (toTop && localValue.value % props.shift_step !== 0) {
				newValue -= props.shift_step;
			}
		}
	}

	onTextValueChange(`${getValueInRange(newValue)}${localUnit.value}`);
}

onBeforeMount(() => {
	removeEvents();
});

onMounted(() => {
	window.removeEventListener('mousemove', dragNumberThrottle);
});
</script>
<style lang="scss">
.znpb-input-number__units-tooltip-wrapper {
	cursor: pointer;
}

.znpb-input-number-unit {
	position: relative;
	.znpb-editor-icon-wrapper.znpb-input-number__dots {
		position: absolute;
		top: 0;
		right: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		flex-grow: 1;
		height: 100%;
		padding-right: 3px;
		font-size: 10px;
		cursor: pointer;
	}
	.zion-input {
		padding-right: 10px;
	}
	input {
		text-align: left;
	}

	&__units {
		position: relative;

		&-tooltip-wrapper {
			padding: 0;
		}
	}

	&--has-units {
		.znpb-input-number__units-multiple {
			color: var(--zb-surface-border-color);
			font-size: 11px;
			font-weight: 500;
			line-height: 1;
			cursor: pointer;
		}
	}
}
.znpb-number-unit-list {
	margin: 0;
	list-style-type: none;
	&__option {
		font-weight: 500;
		cursor: pointer;

		&:last-child {
			border-bottom: 0;
		}
		&:hover,
		&--selected {
			color: var(--zb-surface-text-active-color);
		}
	}
}
.znpb-input-number__units--auto {
	padding: 7px;
	background-color: var(--zb-surface-lighter-color);
}

.znpb-input-number__unitValue {
	padding: 5px;
	font-size: 11px;
	cursor: pointer;
}

.znpb-input-number__isValueDisabled {
	width: 100%;
	height: 100%;
	position: absolute;
	left: 0;
	top: 0;
	display: flex;
	align-items: center;
	justify-content: center;
}
</style>
