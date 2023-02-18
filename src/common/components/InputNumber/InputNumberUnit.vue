<template>
	<div ref="root" class="znpb-input-number-unit">
		<BaseInput
			ref="numberUnitInput"
			:model-value="localRawValue"
			class="znpb-input-number--has-units"
			size="narrow"
			:placeholder="computedPlaceholder"
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
				>
					<template #content>
						<div class="znpb-number-unit-list hg-popper-list">
							<div
								v-for="(unit, i) in units"
								:key="i"
								class="znpb-number-unit-list__option hg-popper-list__item"
								:class="{
									[`znpb-number-unit-list__option--selected`]: activeUnit === unit,
								}"
								@click.stop="changeUnit(unit)"
							>
								{{ unit.length ? unit : '-' }}
							</div>

							<div
								class="znpb-number-unit-list__option hg-popper-list__item"
								:class="{
									[`znpb-number-unit-list__option--selected`]: activeUnit === '',
								}"
								@click.stop="changeUnit('')"
							>
								{{ i18n.__('custom', 'zionbuilder') }}
							</div>
						</div>
					</template>

					<span class="znpb-input-number__unitValue" @click.stop="showUnits = !showUnits">{{
						localUnit.length ? localUnit : '-'
					}}</span>
				</Tooltip>
			</template>
		</BaseInput>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed, onMounted, ref, watch, Ref, nextTick, onBeforeUnmount } from 'vue';
import rafSchd from 'raf-schd';

import { DEFAULT_UNIT_TYPES, ALL_NUMBER_UNITS_TYPES } from '../../data';
import Tooltip from '../tooltip/components/Tooltip.vue';
import BaseInput from '../BaseInput/BaseInput.vue';

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
		units: function () {
			return DEFAULT_UNIT_TYPES;
		},
		default_unit: '',
	},
);

const emit = defineEmits(['update:modelValue', 'linked-value']);

const root: Ref<HTMLElement | null> = ref(null);
const numberUnitInput: Ref<typeof BaseInput | null> = ref(null);
const localRawValue = ref('');
const localValue = ref(0);
const localUnit: Ref<string> = ref('');
const showUnits = ref(false);

// Flag to allow the values to change or not on newValues
let preventWatcher = false;

const defaultUnit = computed(() => {
	return props.default_unit.length ? props.default_unit : props.units[0];
});

const computedPlaceholder = computed(() => {
	const { value, rawValue } = getValuesFromString(props.placeholder);

	return value ?? rawValue;
});

const activeUnit = computed(() => {
	if (props.units.includes(localUnit.value)) {
		return localUnit.value;
	} else if (props.units.includes(localRawValue.value)) {
		return localRawValue.value;
	}

	return defaultUnit.value;
});

watch(
	() => props.modelValue,
	newValue => {
		if (preventWatcher) {
			return;
		}

		const { unit, value, rawValue, unitIsValid } = getValuesFromString(newValue);

		localRawValue.value = rawValue;
		localValue.value = null !== value ? value : getValueInRange(0);

		if (localRawValue.value && localRawValue.value.length) {
			localUnit.value = unitIsValid && unit ? unit : '';
		} else {
			const { unit } = getValuesFromString(props.placeholder);

			// Use default unit or empty string
			localUnit.value = unit && unit.length ? unit : defaultUnit.value ? defaultUnit.value : '';
		}

		preventWatcher = false;
	},
	{
		immediate: true,
	},
);

function isNumeric(value: string | number) {
	return !isNaN(value as number) && !isNaN(parseFloat(value as string));
}

function getValuesFromString(string: string) {
	let unit = null;
	let value = null;
	let rawValue = '';
	let unitIsValid = false;

	if (isNumeric(string)) {
		value = parseFloat(string);
		rawValue = string;
	} else {
		const { value: parsedValue, unit: parsedUnit } = getIntegerAndUnit(string);

		unitIsValid = parsedUnit !== null && props.units.includes(parsedUnit);

		if (parsedValue !== null && parsedUnit !== null) {
			// If the unit is valid, we will split the value and the unit
			rawValue = unitIsValid ? `${parsedValue}` : `${parsedValue}${parsedUnit}`;
			value = parsedValue;
			unit = parsedUnit;
		} else {
			rawValue = string ?? '';
			unit = parsedUnit;
		}
	}

	return {
		unit,
		value,
		rawValue,
		unitIsValid,
	};
}

function changeUnit(newUnit: string) {
	showUnits.value = false;
	localUnit.value = newUnit;

	// Focus the input
	nextTick(() => {
		if (numberUnitInput.value !== null) {
			numberUnitInput.value.focus();
		}
	});

	// Don't proceed if we don't have an existing value
	if ((props.units && props.units.includes(newUnit)) || ALL_NUMBER_UNITS_TYPES.includes(newUnit)) {
		if (localValue.value) {
			onTextValueChange(`${localValue.value}${newUnit}`);
		} else {
			// Only change locally and remove the value
			onTextValueChange('', {
				shouldPreventWatcher: true,
			});
		}
	} else if (newUnit === '') {
		// Clear the value
		onTextValueChange('', {
			shouldPreventWatcher: true,
		});
	} else {
		localUnit.value = '';
		// Clear the value
		onTextValueChange(newUnit, {
			shouldPreventWatcher: true,
		});
	}
}

function onTextValueChange(
	newValue: string,
	flags: {
		shouldPreventWatcher?: boolean;
		updateLocalRawValue?: boolean;
	} = {
		shouldPreventWatcher: false,
		updateLocalRawValue: true,
	},
) {
	const { unit, value, rawValue } = getValuesFromString(newValue);
	const validUnit = unit && unit.length ? unit : localUnit.value;
	const { shouldPreventWatcher = false, updateLocalRawValue = true } = flags;
	preventWatcher = shouldPreventWatcher;

	// Ensure that the input gets updated..
	if (updateLocalRawValue) {
		localRawValue.value = newValue;
	}

	// If the value is empty, prevent returning to default value when the watcher is running
	if (newValue.length === 0) {
		preventWatcher = true;
	}

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
	const match = typeof string === 'string' && string ? string.match(/^([+-]?[0-9]+(?:[.][0-9]+)?)(\D+)?$/) : null;
	const value = match && match[1] ? parseFloat(match[1]) : null;
	const unit = match && match[2] ? match[2] : null;

	return {
		value,
		unit,
	};
}

let mouseDownPositionTop = 0;
let draggingPositionTop = 0;
const dragThreshold = 3;
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

	if (root.value) {
		root.value.ownerDocument.body.style.userSelect = 'none';

		if (root.value.ownerDocument.defaultView) {
			root.value.ownerDocument.defaultView.addEventListener('mousemove', dragNumberThrottle);
			root.value.ownerDocument.defaultView.addEventListener('mouseup', deactivateDragNumber);
		}
	}
}

function canUpdateNumber() {
	return localRawValue.value === '' || isNumeric(localRawValue.value);
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

	if (root.value) {
		root.value.ownerDocument.body.style.userSelect = '';
		root.value.ownerDocument.body.style.pointerEvents = '';
		(root.value.ownerDocument.defaultView as Window).removeEventListener('mousemove', dragNumberThrottle);
	}
}
function removeEvents() {
	deactivateDragNumber();

	if (root.value) {
		(root.value.ownerDocument.defaultView as Window).removeEventListener('mouseup', deactivateDragNumber);
	}
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

		if (root.value) {
			root.value.ownerDocument.body.style.pointerEvents = 'none';
		}

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

	// Set the default unit if we do not have a valid one
	const unit = localUnit.value && ALL_NUMBER_UNITS_TYPES.includes(localUnit.value) ? localUnit.value : '';
	onTextValueChange(`${newValue}${unit}`, {
		updateLocalRawValue: false,
	});
}

onBeforeUnmount(() => {
	removeEvents();
});

onMounted(() => {
	if (root.value) {
		(root.value.ownerDocument.defaultView as Window).removeEventListener('mousemove', dragNumberThrottle);
	}
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
	opacity: 0.6;
}
</style>
