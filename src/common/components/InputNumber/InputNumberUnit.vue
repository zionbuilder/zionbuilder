<template>
	<div class="znpb-input-number-unit">
		<BaseInput
			ref="numberUnitInput"
			v-model="computedStringValue"
			class="znpb-input-number--has-units"
			size="narrow"
			:placeholder="placeholder"
			@mousedown="actNumberDrag"
			@touchstart.prevent.passive="actNumberDrag"
			@mouseup="deactivateDragNumber"
			@keydown="onKeyDown"
		/>
	</div>
</template>

<script lang="ts" setup>
import { computed, onBeforeMount, onMounted } from 'vue';
import rafSchd from 'raf-schd';

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
	}>(),
	{
		modelValue: '',
		min: -Infinity,
		max: Infinity,
		step: 1,
		shift_step: 5,
		placeholder: '',
	},
);

const emit = defineEmits(['update:modelValue', 'linked-value']);

let mouseDownPositionTop = 0;
let draggingPositionTop = 0;
let dragThreshold = 3;
let shiftDrag = false;
let toTop = false;
let directionReset = 0;
let draggingCached = 0;
let dragging = false;
const dragNumberThrottle = rafSchd(dragNumber);

const computedValueUnit = computed(() => {
	return getIntegerAndUnit(props.modelValue);
});

const computedIntegerValue = computed({
	get() {
		return computedValueUnit.value.value !== null ? computedValueUnit.value.value : props.min;
	},
	set(newValue: number) {
		computedStringValue.value = `${newValue}${computedUnitValue.value}`;
	},
});

const computedUnitValue = computed(() => (computedValueUnit.value.unit !== null ? computedValueUnit.value.unit : ''));

const computedStringValue = computed({
	get() {
		return props.modelValue ? props.modelValue : '';
	},
	set(newValue: string) {
		// Get the value and unit
		const integerAndUnit = getIntegerAndUnit(newValue);

		// Get the value in range
		const value = integerAndUnit.value !== null ? getValueInRange(integerAndUnit.value) : '';

		// Get the unit in range
		const unit = integerAndUnit.unit !== null ? integerAndUnit.unit : '';

		if (value !== '' || unit !== '') {
			emit('update:modelValue', `${value}${unit}`);
		} else {
			emit('update:modelValue', newValue);
		}
	},
});

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

function actNumberDrag(event: MouseEvent) {
	dragging = true;
	draggingCached = computedIntegerValue.value;
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
	const integerAndUnit = getIntegerAndUnit(computedStringValue.value);
	return computedStringValue.value === '' || integerAndUnit.value !== null;
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

		newValue = computedIntegerValue.value + increment;
		if (shiftDrag) {
			newValue = newValue % props.shift_step ? Math.ceil(newValue / props.shift_step) * props.shift_step : newValue;
			if (toTop && computedIntegerValue.value % props.shift_step !== 0) {
				newValue -= props.shift_step;
			}
		}
	}

	// Check that value is in between min and max
	computedIntegerValue.value = getValueInRange(newValue);
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
	padding: 0;
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
</style>
