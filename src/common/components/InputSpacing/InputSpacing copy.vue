<template>
	<div class="znpb-optSpacing">
		<div
			class="znpb-optSpacing-margin"
			:class="{
				'znpb-optSpacing--linked': linkedMargin,
				'znpb-optSpacing--hover': activeHover && activeHover.position.includes('margin'),
				[`znpb-optSpacing--hover-${activeHover ? activeHover.position : ''}`]: activeHover,
			}"
		>
			<div
				v-for="position in marginPositionId"
				:key="position.position"
				:class="{
					[`znpb-optSpacing-${position.position}`]: true,
				}"
				class="znpb-optSpacing-value znpb-optSpacing-value--margin"
				@mouseenter="activeHover = position"
				@mouseleave="activeHover = null"
				@mousedown="startDragging($event, position)"
			>
				<InputNumberUnit
					:model-value="computedValues[position.position]"
					:units="['px', 'rem', 'pt', 'vh', '%']"
					:step="1"
					default-unit="px"
					:placeholder="
						placeholder && typeof placeholder[position.position] !== 'undefined' ? placeholder[position.position] : '-'
					"
					@update:model-value="onValueUpdated(position.position, 'margin', $event)"
				/>

				<ChangesBullet
					v-if="computedValues[position.position]"
					:content="i18n.__('Discard changes', 'zionbuilder')"
					@remove-styles="onDiscardChanges(position.position)"
				/>
			</div>
			<div class="znpb-optSpacing-labelWrapper">
				<span class="znpb-optSpacing-label">{{ i18n.__('Margin', 'zionbuilder') }}</span>
				<Icon
					:icon="linkedMargin ? 'link' : 'unlink'"
					:title="linkedMargin ? i18n.__('unlink', 'zionbuilder') : i18n.__('Link', 'zionbuilder')"
					:size="12"
					class="znpb-optSpacing-link"
					:class="{
						'znpb-optSpacing-link--linked': linkedMargin,
					}"
					@click="linkValues('margin')"
				></Icon>
			</div>
			<div class="znpb-optSpacing-svg">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 320 186">
					<path
						v-for="position in marginPositionId"
						:key="position.position"
						:cursor="position.svg.cursor"
						:d="position.svg.d"
						:class="{
							[`znpb-optSpacing--path-${position.position}`]: true,
						}"
						@mouseenter="activeHover = position"
						@mouseleave="activeHover = null"
						@mousedown="startDragging($event, position)"
					/>
				</svg>
			</div>
		</div>
		<div
			class="znpb-optSpacing-padding"
			:class="{
				'znpb-optSpacing--linked': linkedPadding,
				'znpb-optSpacing--hover': activeHover && activeHover.position.includes('padding'),
				[`znpb-optSpacing--hover-${activeHover ? activeHover.position : ''}`]: activeHover,
			}"
		>
			<div
				v-for="position in paddingPositionId"
				:key="position.position"
				:class="{
					[`znpb-optSpacing-${position.position}`]: true,
				}"
				class="znpb-optSpacing-value znpb-optSpacing-value--padding"
				@mouseenter="activeHover = position"
				@mouseleave="activeHover = null"
				@mousedown="startDragging($event, position)"
			>
				<InputNumberUnit
					:model-value="computedValues[position.position]"
					:units="['px', 'rem', 'pt', 'vh', '%']"
					:step="1"
					default-unit="px"
					:min="0"
					:placeholder="
						placeholder && typeof placeholder[position.position] !== 'undefined' ? placeholder[position.position] : '-'
					"
					@update:model-value="onValueUpdated(position.position, 'padding', $event)"
				/>

				<ChangesBullet
					v-if="computedValues[position.position]"
					:content="i18n.__('Discard changes', 'zionbuilder')"
					@remove-styles="onDiscardChanges(position.position)"
				/>
			</div>

			<div class="znpb-optSpacing-labelWrapper">
				<span class="znpb-optSpacing-label">{{ i18n.__('Padding', 'zionbuilder') }}</span>
				<Icon
					:icon="linkedPadding ? 'link' : 'unlink'"
					:title="linkedPadding ? i18n.__('unlink', 'zionbuilder') : i18n.__('link', 'zionbuilder')"
					:size="12"
					class="znpb-optSpacing-link"
					:class="{
						'znpb-optSpacing-link--linked': linkedPadding,
					}"
					@click="linkValues('padding')"
				></Icon>
			</div>
			<div class="znpb-optSpacing-svg">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 214 108">
					<path
						v-for="position in paddingPositionId"
						:key="position.position"
						:cursor="position.svg.cursor"
						:d="position.svg.d"
						:class="{
							[`znpb-optSpacing--path-${position.position}`]: true,
						}"
						@mouseenter="activeHover = position"
						@mouseleave="activeHover = null"
						@mousedown="startDragging($event, position)"
					/>
				</svg>
			</div>
		</div>
		<span v-if="activeHover" class="znpb-optSpacing-info">{{ activeHover.title }}</span>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputSpacing',
	directives: {
		clickOutside,
	},
};
</script>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { computed, ref, Ref } from 'vue';
import { InputNumberUnit } from '../InputNumber';
import rafSchd from 'raf-schd';
import clickOutside from '../../directives/click-outside.ts';

type PositionId =
	| 'margin-top'
	| 'margin-right'
	| 'margin-bottom'
	| 'margin-left'
	| 'padding-top'
	| 'padding-right'
	| 'padding-bottom'
	| 'padding-left';

type Type = 'margin' | 'padding';

const props = withDefaults(
	defineProps<{
		modelValue?: Partial<Record<PositionId, string>>;
		placeholder?: Partial<Record<PositionId, string>>;
	}>(),
	{
		modelValue: () => {
			return {};
		},
		placeholder: () => {
			return {};
		},
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: Partial<Record<PositionId, string>>): void;
}>();

interface Position {
	position: PositionId;
	type: Type;
	title: string | undefined;
	svg: {
		cursor: string;
		d: string;
	};
	dragDirection: 'vertical' | 'horizontal';
}

const marginPositionId: Position[] = [
	{
		position: 'margin-top',
		type: 'margin',
		title: i18n.__('Margin top', 'zionbuilder'),
		svg: {
			cursor: 'n-resize',
			d: 'M0 0h320l-50 36H50L0 0Z',
		},
		dragDirection: 'vertical',
	},
	{
		position: 'margin-right',
		type: 'margin',
		title: i18n.__('Margin right', 'zionbuilder'),
		svg: {
			cursor: 'e-resize',
			d: 'm320 183-50-36V39l50-36v180Z',
		},
		dragDirection: 'horizontal',
	},
	{
		position: 'margin-bottom',
		type: 'margin',
		title: i18n.__('Margin bottom', 'zionbuilder'),
		svg: {
			cursor: 's-resize',
			d: 'M50 150h220l50 36H0l50-36Z',
		},
		dragDirection: 'vertical',
	},
	{
		position: 'margin-left',
		type: 'margin',
		title: i18n.__('Margin left', 'zionbuilder'),
		svg: {
			cursor: 'w-resize',
			d: 'm0 3 50 36v108L0 183V3Z',
		},
		dragDirection: 'horizontal',
	},
];
const paddingPositionId: Position[] = [
	{
		position: 'padding-top',
		type: 'padding',
		title: i18n.__('Padding top', 'zionbuilder'),
		svg: {
			cursor: 'n-resize',
			d: 'M0 0h214l-50 36H50L0 0Z',
		},
		dragDirection: 'vertical',
	},
	{
		position: 'padding-right',
		type: 'padding',
		title: i18n.__('Padding right', 'zionbuilder'),
		svg: {
			cursor: 'e-resize',
			d: 'm214 105-50-36V39l50-36v102Z',
		},
		dragDirection: 'horizontal',
	},
	{
		position: 'padding-bottom',
		type: 'padding',
		title: i18n.__('Padding bottom', 'zionbuilder'),
		svg: {
			cursor: 's-resize',
			d: 'M214 108H0l50-36h114l50 36Z',
		},
		dragDirection: 'vertical',
	},
	{
		position: 'padding-left',
		type: 'padding',
		title: i18n.__('Padding left', 'zionbuilder'),
		svg: {
			cursor: 'w-resize',
			d: 'm0 3 50 36v30L0 105V3Z',
		},
		dragDirection: 'horizontal',
	},
];
const allowedValues = [...marginPositionId, ...paddingPositionId].map(position => position.position);

/**
 * Refs
 */
const activeHover: Ref<Position | null> = ref(null);
const lastChanged: Ref<{ position: PositionId; type: Type } | null> = ref(null);
const popupInput = ref<InstanceType<typeof InputNumberUnit> | null>(null);

function onDiscardChanges(position: PositionId) {
	const clonedModelValue: Partial<Record<PositionId, string>> = { ...props.modelValue };
	delete clonedModelValue[position];

	emit('update:modelValue', clonedModelValue);
}

const computedValues = computed({
	get() {
		const values: Record<PositionId, string> = {};

		Object.keys(props.modelValue).forEach(optionId => {
			if (allowedValues.includes(optionId as PositionId)) {
				values[optionId] = props.modelValue[optionId];
			}
		});

		return values;
	},
	set(newValues: Record<PositionId, string>) {
		emit('update:modelValue', newValues);
	},
});

function onValueUpdated(sizePosition: PositionId, type: Type, newValue: string) {
	const isLinked = type === 'margin' ? linkedMargin : linkedPadding;

	// Keep a track of the last changed position so we can use it for linking values
	lastChanged.value = {
		position: sizePosition,
		type: type,
	};

	if (isLinked.value) {
		const valuesToUpdate = type === 'margin' ? marginPositionId : paddingPositionId;
		const updatedValues: Partial<Record<PositionId, string>> = {};

		valuesToUpdate.forEach(position => (updatedValues[position.position] = newValue));

		computedValues.value = {
			...props.modelValue,
			...updatedValues,
		};
	} else {
		computedValues.value = {
			...props.modelValue,
			[sizePosition]: newValue,
		};
	}
}

/**
 * Values linking
 * - If a value was manually changed, change all the values to this value
 * - If no value was changed, try to find a modified value and apply it to all
 */
const linkedMargin = ref(isLinked('margin'));
const linkedPadding = ref(isLinked('padding'));

function linkValues(type: Type) {
	const valueToChange = type === 'margin' ? linkedMargin : linkedPadding;
	valueToChange.value = !valueToChange.value;

	// Set the same value for all
	if (valueToChange.value) {
		if (lastChanged.value && lastChanged.value.type === type) {
			onValueUpdated(lastChanged.value.position, type, computedValues.value[lastChanged.value.position]);
		} else {
			// Find a position that has a saved value
			const valuesToCheck = type === 'margin' ? marginPositionId : paddingPositionId;
			const savedValueConfig = valuesToCheck.find(
				positionConfig => computedValues.value[positionConfig.position] !== 'undefined',
			);

			if (savedValueConfig) {
				onValueUpdated(savedValueConfig.position, type, computedValues.value[savedValueConfig.position]);
			}
		}
	}
}

function isLinked(type: Type) {
	const valuesToCheck = type === 'margin' ? marginPositionId : paddingPositionId;

	return valuesToCheck.every(position => {
		return (
			computedValues.value[position.position] &&
			computedValues.value[position.position] === computedValues.value[`${type}-top`]
		);
	});
}

/**
 * Dragging
 */
let startMousePosition: { clientY: number; clientX: number } | null;

let dragDirection: 'vertical' | 'horizontal';

let initialValue: { value: number; unit: string } | null;

let draggingConfig: {
	positionConfig: Position;
	position: PositionId;
	type: Type;
	initialValue: typeof initialValue;
	activeLinkStatus: boolean;
	activeLinkComputedValue: Ref<boolean>;
} | null;

const dragThreshold = 0;

const validUnits = [
	{
		type: 'px',
		isModifiable: true,
	},
	{
		type: '%',
		isModifiable: true,
	},
	{
		type: 'vw',
		isModifiable: true,
	},
	{
		type: 'vh',
		isModifiable: true,
	},
	{
		type: 'rem',
		isModifiable: true,
	},
	{
		type: 'em',
		isModifiable: true,
	},
	{
		type: 'pt',
		isModifiable: true,
	},
	{
		type: 'auto',
		isModifiable: false,
	},
	{
		type: 'initial',
		isModifiable: false,
	},
	{
		type: 'unset',
		isModifiable: false,
	},
];

// refs
const isDragging = ref(false);

function startDragging(event: MouseEvent, positionConfig: Position) {
	const { clientY, clientX } = event;

	// Save the initial values
	startMousePosition = {
		clientY,
		clientX,
	};

	// Set the drag direction
	dragDirection = positionConfig.dragDirection;

	const { position, type } = positionConfig;

	// prevent selection
	document.body.style.userSelect = 'none';

	initialValue = getSplitValue(position);
	const unit = initialValue && initialValue.unit;
	const validUnit = validUnits.find(singleUnit => singleUnit.type === unit);

	if (validUnit && validUnit.isModifiable) {
		const linkType = type === 'margin' ? linkedMargin : linkedPadding;

		// Set initial value
		draggingConfig = {
			positionConfig,
			position,
			type,
			initialValue,

			// Link status
			activeLinkStatus: linkType.value,
			activeLinkComputedValue: linkType,
		};

		window.addEventListener('mousemove', rafDragValue);
		window.addEventListener('mouseup', rafDeactivateDragging);
		window.addEventListener('keydown', onKeyDown);
		window.addEventListener('keyup', onKeyUp);
	}
}

function getSplitValue(position: PositionId) {
	const savedValue = computedValues.value[position] ? computedValues.value[position] : '0px';
	const splitValue = savedValue.match(/^([+-]?(?:\d+|\d*\.\d+))([a-z]*|%)$/);

	if (!splitValue) {
		return null;
	}

	return {
		value: parseInt(splitValue[1]),
		unit: splitValue[2],
	};
}

function onKeyDown(event: KeyboardEvent) {
	if (isDragging.value) {
		const { activeLinkStatus, activeLinkComputedValue } = draggingConfig as Exclude<typeof draggingConfig, null>;

		if (!activeLinkStatus && event.ctrlKey) {
			activeLinkComputedValue.value = true;
		} else {
			activeLinkComputedValue.value = activeLinkStatus;
		}
	}
}

function onKeyUp(event: KeyboardEvent) {
	if (isDragging.value) {
		const { activeLinkComputedValue } = draggingConfig as Exclude<typeof draggingConfig, null>;

		if (event.ctrlKey) {
			activeLinkComputedValue.value = false;
		}
	}
}

function deactivateDragging() {
	document.body.style.userSelect = '';
	document.body.style.pointerEvents = '';

	// cancel dragging raf
	rafDragValue.cancel();

	// Remove events
	window.removeEventListener('mousemove', rafDragValue);
	window.removeEventListener('mouseup', rafDeactivateDragging);

	isDragging.value = false;
	startMousePosition = null;
	initialValue = null;
	draggingConfig = null;
}

function dragValue(event: MouseEvent) {
	const { clientX, clientY } = event;
	document.body.style.pointerEvents = 'none';

	const movedAmount =
		dragDirection === 'vertical'
			? Math.ceil(startMousePosition!.clientY - clientY)
			: Math.ceil(startMousePosition!.clientX - clientX) * -1;

	if (Math.abs(movedAmount) > dragThreshold) {
		const positionConfig = draggingConfig ? draggingConfig.positionConfig : null;
		isDragging.value = true;
		activeHover.value = positionConfig;

		setDraggingValue(movedAmount - dragThreshold, event);
	}
}

function setDraggingValue(newValue: number, event: MouseEvent) {
	const { position, type, initialValue } = draggingConfig as Exclude<typeof draggingConfig, null>;

	if (!initialValue) return;
	const { value, unit } = initialValue;

	// const updatedValue = event.shiftKey ? initialValue + increment).toFixed(12) : newValue + value
	let updatedValue = newValue + value;

	// Check if the
	if (event.shiftKey) {
		updatedValue = Math.round(updatedValue / 5) * 5;
	}

	const valueToUpdate = `${updatedValue}${unit}`;

	// Update the value
	onValueUpdated(position, type, valueToUpdate);
}

const rafDragValue = rafSchd(dragValue);
const rafDeactivateDragging = rafSchd(deactivateDragging);
</script>

<style lang="scss">
.znpb-optSpacing {
	position: relative;
	width: 320px;
	height: 186px;
	margin: 0 auto;
	outline-style: none;
	user-select: none;

	&-margin {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;

		justify-items: center;
	}

	&-padding {
		position: absolute;
		top: 39px;
		left: 53px;
		width: 214px;
		height: 108px;

		justify-items: center;
	}

	&-value {
		position: absolute;
		z-index: 1;
		max-width: 61px;
		padding: 2px;
		color: var(--zb-surface-text-color);
		font-size: 11px;
		font-weight: 500;
		line-height: 1;
		text-overflow: ellipsis;
		white-space: nowrap;
		user-select: none;
		display: flex;
		align-items: center;

		& input {
			max-width: 42px;
			height: 26px;
			color: var(--zb-surface-text-color);
			font-size: 11px;
			font-weight: bold;
			text-align: center;
			background: transparent;
			border: 0;

			&:focus {
				outline: none;
			}
		}

		& .znpb-options-has-changes-wrapper {
			position: absolute;
			right: -3px;
			top: -3px;
		}
	}

	&-margin-top,
	&-padding-top,
	&-margin-bottom,
	&-padding-bottom {
		left: 50%;
		transform: translateX(-50%);
	}

	&-margin-top,
	&-padding-top {
		top: 3px;
	}

	&-margin-bottom,
	&-padding-bottom {
		bottom: 3px;
	}

	&-margin-left,
	&-padding-left,
	&-margin-right,
	&-padding-right {
		top: 50%;
		transform: translateY(-50%);
		flex-direction: column;
		max-width: 42px;

		.znpb-options-has-changes-wrapper {
			margin-top: 5px;
		}
	}

	&-margin-left,
	&-padding-left {
		left: 4px;
	}

	&-margin-right,
	&-padding-right {
		right: 4px;
	}

	&-popup {
		position: absolute;
		top: 0;
		left: 0;
		z-index: 1;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 100%;
		height: 100%;

		&::before {
			content: '';
			position: absolute;
			top: -40px;
			right: -54px;
			bottom: -40px;
			left: -54px;
			z-index: -1;
			background: var(--zb-surface-color);
			opacity: 0.5;
		}

		&Inner {
			position: relative;
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			flex-direction: column;
			justify-content: center;
			width: 214px;
			height: 108px;
			padding: 16px;
			background: var(--zb-surface-color);
			box-shadow: var(--zb-dropdown-shadow);
			border: 1px solid var(--zb-dropdown-border-color);
			border-radius: 4px;

			-webkit-box-direction: normal;
			-webkit-box-orient: vertical;
			-webkit-box-pack: center;
			-ms-flex-direction: column;
			-ms-flex-pack: center;
		}

		&Close {
			position: absolute;
			top: 10px;
			right: 10px;
			color: var(--zb-surface-icon-color);
			transition: color 0.1s;
			cursor: pointer;

			&:hover {
				color: var(--zb-surface-icon-active-color);
			}

			& svg {
				font-size: 10px;
			}
		}

		&__input-title {
			display: flex;
			align-items: center;
			margin-bottom: 10px;
			color: var(--zb-surface-text-hover-color);
			font-family: var(--zb-font-stack);
			font-size: 13px;
			font-weight: 500;
			line-height: 14px;
		}
	}

	&-info {
		position: absolute;
		top: 50%;
		left: 50%;
		z-index: 1;
		display: flex;
		justify-content: center;
		align-items: center;
		width: 108px;
		height: 30px;
		margin: -15px 0 0 -54px;
		color: #858585;
		font-size: 10px;
		font-weight: bold;
		line-height: 1;
		text-transform: uppercase;
	}

	&-labelWrapper {
		position: absolute;
		top: 4px;
		left: 24px;
		z-index: 1;
		display: -webkit-box;
		display: -ms-flexbox;
		display: flex;
		align-items: center;
		pointer-events: none;

		-webkit-box-align: center;
		-ms-flex-align: center;
	}

	&-label {
		color: #686868;
		font-size: 9px;
		font-weight: bold;
		line-height: 1;
		text-transform: uppercase;
		pointer-events: none;
	}

	&-link {
		margin-left: 4px;
		color: #858585;
		font-size: 12px;
		cursor: pointer;
		pointer-events: all;

		&--linked {
			color: var(--zb-secondary-color);
		}

		& svg {
			display: block;
			width: 1em;
			height: 1em;

			fill: currentColor;
		}
	}

	&-svg {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;

		& svg {
			display: block;
			width: 100%;
			height: 100%;

			& path {
				transition: fill 0.1s;

				&:nth-child(odd) {
					fill: #ebebeb;
				}

				&:nth-child(even) {
					fill: #f1f1f1;
				}

				&:hover {
					fill: #e6e6e6;
				}
			}
		}

		@at-root .znpb-theme-dark .znpb-optSpacing-svg svg {
			& path {
				&:nth-child(odd) {
					fill: #3e3e42;
				}

				&:nth-child(even) {
					fill: #38383c;
				}
			}
		}

		@at-root .znpb-theme-dark {
			// Margin
			.znpb-optSpacing--hover-margin-top
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-margin-top, .znpb-optSpacing--hover-margin-left
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-margin-left, .znpb-optSpacing--hover-margin-bottom
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-margin-bottom, .znpb-optSpacing--hover-margin-right
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-margin-right, // Paddings
			.znpb-optSpacing--hover-padding-top
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-padding-top, .znpb-optSpacing--hover-padding-left
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-padding-left, .znpb-optSpacing--hover-padding-bottom
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-padding-bottom, .znpb-optSpacing--hover-padding-right
			.znpb-optSpacing-svg
			svg
			.znpb-optSpacing--path-padding-right, // Hovered states
			.znpb-optSpacing--linked.znpb-optSpacing--hover
			.znpb-optSpacing-svg
			svg
			path {
				fill: #3a3a3e;
			}
		}
	}
}

body .znpb-optSpacing .zion-input {
	background: var(--zb-surface-color);
	border: none;
	padding: 0;

	& input {
		background: transparent;
		padding: 5.5px 4px;
		font-size: 11px;
	}
}
</style>
