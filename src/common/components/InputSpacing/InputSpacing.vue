<template>
	<div class="znpb-optSpacing" @keydown="checkForOppositeChange" @keyup="oppositeChange = false">
		<div class="znpb-optSpacing-margin">
			<div
				v-for="position in marginPositionId"
				:key="position.position"
				:class="{
					[`znpb-optSpacing-${position.position}`]: true,
				}"
				class="znpb-optSpacing-value znpb-optSpacing-value--margin"
				@mouseenter="activeHover = position"
				@mouseleave="activeHover = null"
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
					:content="translate('discard_changes')"
					@remove-styles="onDiscardChanges(position.position)"
				/>
			</div>

			<div class="znpb-optSpacing-labelWrapper">
				<span class="znpb-optSpacing-label">Margin</span>
				<Icon
					:icon="linkedMargin ? 'link' : 'unlink'"
					:title="linkedMargin ? translate('unlink') : translate('link')"
					:size="12"
					class="znpb-optSpacing-link"
					:class="{
						'znpb-optSpacing-link--linked': linkedMargin,
					}"
					@click="linkValues('margin')"
				/>
			</div>
		</div>
		<div class="znpb-optSpacing-padding">
			<div
				v-for="position in paddingPositionId"
				:key="position.position"
				:class="{
					[`znpb-optSpacing-${position.position}`]: true,
				}"
				class="znpb-optSpacing-value znpb-optSpacing-value--padding"
				@mouseenter="activeHover = position"
				@mouseleave="activeHover = null"
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
					:content="translate('discard_changes')"
					@remove-styles="onDiscardChanges(position.position)"
				/>
			</div>

			<div class="znpb-optSpacing-labelWrapper">
				<span class="znpb-optSpacing-label">Padding</span>
				<Icon
					:icon="linkedPadding ? 'link' : 'unlink'"
					:title="linkedPadding ? translate('unlink') : translate('link')"
					:size="12"
					class="znpb-optSpacing-link"
					:class="{
						'znpb-optSpacing-link--linked': linkedPadding,
					}"
					@click="linkValues('padding')"
				></Icon>
			</div>
		</div>
		<span class="znpb-optSpacing-info">{{ activeHover ? activeHover.title : '' }}</span>
	</div>
</template>

<script lang="ts">
export default {
	name: 'InputSpacing',
};
</script>

<script lang="ts" setup>
import { computed, ref, Ref } from 'vue';
import { InputNumberUnit } from '../InputNumber';
import { translate } from '../../modules/i18n';

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
		title: translate('margin-top'),
		svg: {
			cursor: 'n-resize',
			d: 'M0 0h320l-50 36H50L0 0Z',
		},
		dragDirection: 'vertical',
	},
	{
		position: 'margin-right',
		type: 'margin',
		title: translate('margin-right'),
		svg: {
			cursor: 'e-resize',
			d: 'm320 183-50-36V39l50-36v180Z',
		},
		dragDirection: 'horizontal',
	},
	{
		position: 'margin-bottom',
		type: 'margin',
		title: translate('margin-bottom'),
		svg: {
			cursor: 's-resize',
			d: 'M50 150h220l50 36H0l50-36Z',
		},
		dragDirection: 'vertical',
	},
	{
		position: 'margin-left',
		type: 'margin',
		title: translate('margin-left'),
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
		title: translate('padding-top'),
		svg: {
			cursor: 'n-resize',
			d: 'M0 0h214l-50 36H50L0 0Z',
		},
		dragDirection: 'vertical',
	},
	{
		position: 'padding-right',
		type: 'padding',
		title: translate('padding-right'),
		svg: {
			cursor: 'e-resize',
			d: 'm214 105-50-36V39l50-36v102Z',
		},
		dragDirection: 'horizontal',
	},
	{
		position: 'padding-bottom',
		type: 'padding',
		title: translate('padding-bottom'),
		svg: {
			cursor: 's-resize',
			d: 'M214 108H0l50-36h114l50 36Z',
		},
		dragDirection: 'vertical',
	},
	{
		position: 'padding-left',
		type: 'padding',
		title: translate('padding-left'),
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
const oppositeChange = ref(false);
const activeHover: Ref<Position | null> = ref(null);
const lastChanged: Ref<{ position: PositionId; type: Type } | null> = ref(null);

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
		const oppositePosition = getReversedPosition(sizePosition);

		const newValues = {
			...props.modelValue,
			[sizePosition]: newValue,
		};

		// Check to see if we need to also change the opposite position
		if (oppositeChange.value) {
			newValues[oppositePosition] = newValue;
		}

		// Check to see if we need to change opposite spacing
		computedValues.value = newValues;
	}
}

function getReversedPosition(position: string) {
	const typeAndPosition = position.split(/-/);
	const positionLocation = typeAndPosition[1];
	let reversePositionLocation;

	switch (positionLocation) {
		case 'top':
			reversePositionLocation = 'bottom';
			break;
		case 'bottom':
			reversePositionLocation = 'top';
			break;
		case 'left':
			reversePositionLocation = 'right';
			break;
		case 'right':
			reversePositionLocation = 'left';
			break;
	}

	return `${typeAndPosition[0]}-${reversePositionLocation}`;
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

function checkForOppositeChange(e: KeyboardEvent) {
	const controlKey = window.navigator.userAgent.indexOf('Macintosh') >= 0 ? 'metaKey' : 'ctrlKey';
	if (e[controlKey]) {
		oppositeChange.value = true;
	}
}
</script>

<style lang="scss">
.znpb-optSpacing {
	display: flex;
	align-items: center;
	justify-content: center;
	position: relative;
	height: 180px;

	&-margin {
		width: 100%;
		height: 100%;
		position: relative;
	}

	&-margin::before,
	&-padding::before {
		content: '';
		position: absolute;
		top: 16px;
		left: 8px;
		right: 8px;
		bottom: 16px;
		border: 2px solid var(--zb-surface-lightest-color);
		border-radius: 4px;
	}

	&-padding-top,
	&-margin-top {
		top: 0;
		left: 50%;
		margin-left: -30px;
	}

	&-padding-right,
	&-margin-right {
		top: 50%;
		right: 0;
		margin-top: -16px;
	}

	&-padding-bottom,
	&-margin-bottom {
		bottom: 0;
		left: 50%;
		margin-left: -30px;
	}

	&-padding-left,
	&-margin-left {
		top: 50%;
		left: 0;
		margin-top: -16px;
	}

	&-margin-bottom,
	&-margin-left {
		z-index: 1;
	}

	&-padding {
		position: absolute;
		width: calc(100% - 130px);
		height: calc(100% - 74px);
	}

	&-value {
		position: absolute;
		box-shadow: 0 0 0 4px var(--zb-surface-color);
	}

	&-label {
		font-size: 8px;
		font-weight: 700;
		line-height: 1;
		text-transform: uppercase;
		pointer-events: none;

		&Wrapper {
			position: absolute;
			top: 4px;
			left: 8px;
			z-index: 1;
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			align-items: center;
			pointer-events: none;
			-webkit-box-align: center;
			-ms-flex-align: center;
		}
	}

	&-link {
		margin-left: 4px;
		font-size: 11px;
		cursor: pointer;
		pointer-events: all;

		&--linked {
			color: var(--zb-secondary-color);
		}

		svg {
			display: block;
			width: 1em;
			height: 1em;
			fill: currentColor;
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
		width: 60px;
		height: 32px;
		margin: -16px 0 0 -30px;
		font-size: 10px;
		font-weight: bold;
		text-transform: lowercase;
		text-align: center;
		word-spacing: 9999rem;
		line-height: 1.2;
	}

	& .znpb-input-number-unit {
		background: var(--zb-surface-color);
		width: 60px;
		height: 32px;
		border-radius: 3px;
		border: 0;
	}

	& .znpb-options-has-changes-wrapper {
		position: absolute;
		right: -3px;
		top: -3px;
	}
}
</style>
