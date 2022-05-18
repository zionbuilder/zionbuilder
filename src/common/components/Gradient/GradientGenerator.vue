<template>
	<div class="znpb-gradient-wrapper">
		<GradientBoard
			v-if="!saveToLibrary"
			:config="computedValue"
			:activegrad="activeGradient"
			@change-active-gradient="changeActive($event)"
			@position-changed="changePosition($event)"
		/>

		<ActionsOverlay v-else>
			<GradientBoard
				:config="computedValue"
				:activegrad="activeGradient"
				@change-active-gradient="changeActive($event)"
				@position-changed="changePosition($event)"
			/>

			<template #actions>
				<span v-if="!showPresetInput" class="znpb-gradient__show-preset" @click="showPresetInput = true">{{
					$translate('save_to_library')
				}}</span>

				<PresetInput v-else @save-preset="addGlobalPattern" @cancel="showPresetInput = false" />

				<Icon
					v-if="!showPresetInput"
					icon="delete"
					:bg-size="30"
					class="znpb-gradient-wrapper__delete-gradient"
					@click.stop="deleteGradientValue"
				/>
			</template>
		</ActionsOverlay>

		<div class="znpb-gradient-elements-wrapper">
			<Sortable
				v-model="computedValue"
				class="znpb-admin-colors__container"
				:handle="null"
				:drag-delay="0"
				:drag-treshold="10"
				:disabled="false"
				:revert="true"
				axis="horizontal"
			>
				<GradientElement
					v-for="(gradient, i) in computedValue"
					:key="i"
					class="znpb-gradient-elements__delete-button"
					:config="gradient"
					:show-remove="computedValue.length > 1"
					:is-active="activeGradientIndex === i"
					@change-active-gradient="changeActive(i)"
					@delete-gradient="deleteGradient(gradient)"
				/>
			</Sortable>
			<Icon icon="plus" class="znpb-colorpicker-add-grad" @click="addGradientConfig" />
		</div>

		<GradientOptions v-model="activeGradient" />
	</div>
</template>

<script lang="ts">
export default {
	name: 'GradientGenerator',
};
</script>

<script lang="ts" setup>
import { ref, computed, inject, nextTick } from 'vue';
import { applyFilters } from '@zb/hooks';
import { getDefaultGradient } from '../../utils/';
import Icon from '../Icon/Icon.vue';
import GradientBoard from './GradientBoard.vue';
import GradientOptions from './GradientOptions.vue';
import GradientElement from './GradientElement.vue';
import PresetInput from './PresetInput.vue';
import { Sortable } from '../sortable';
import { ActionsOverlay } from '../ActionsOverlay';
import { generateUID } from '@zb/utils';

import type { Gradient, Position } from './GradientBar.vue';

const props = withDefaults(
	defineProps<{
		modelValue?: Gradient[];
		saveToLibrary?: boolean;
	}>(),
	{
		saveToLibrary: true,
	},
);

const emit = defineEmits<{
	(e: 'update:modelValue', value: Gradient[] | null): void;
}>();

const showPresetInput = ref(false);
const activeGradientIndex = ref(0);

// This should be provided by Apps that are using this component
const useBuilderOptions = inject('builderOptions');
const { addLocalGradient, addGlobalGradient } = useBuilderOptions();

const computedValue = computed({
	get() {
		const clonedValue = JSON.parse(JSON.stringify(props.modelValue ?? getDefaultGradient()));
		return applyFilters('zionbuilder/options/model', clonedValue);
	},
	set(newValue: Gradient[]) {
		emit('update:modelValue', newValue);
	},
});

const activeGradient = computed({
	get() {
		return computedValue.value[activeGradientIndex.value];
	},
	set(newValue: Gradient) {
		const valueToSend = [...computedValue.value];
		valueToSend[activeGradientIndex.value] = newValue;

		computedValue.value = valueToSend;
	},
});

function addGlobalPattern(name: string, type?: string) {
	showPresetInput.value = false;

	const defaultGradient = {
		id: generateUID(),
		name: name,
		config: computedValue.value,
	};

	type === 'local' ? addLocalGradient(defaultGradient) : addGlobalGradient(defaultGradient);
}

function deleteGradient(gradientConfig: Gradient) {
	const deletedGradientIndex = computedValue.value.indexOf(gradientConfig);

	// Set the previous color as active
	if (activeGradient.value === gradientConfig) {
		if (deletedGradientIndex > 0) {
			activeGradientIndex.value = deletedGradientIndex - 1;
		} else {
			activeGradientIndex.value = deletedGradientIndex + 1;
		}
	} else {
		// check if the deleted gradient had an index lower than the active gradient
		if (deletedGradientIndex < activeGradientIndex.value) {
			activeGradientIndex.value = activeGradientIndex.value - 1;
		}
	}

	const updatedValues = computedValue.value.slice(0);
	updatedValues.splice(deletedGradientIndex, 1);

	// Send the new value to parent
	computedValue.value = updatedValues;
}

function addGradientConfig() {
	const defaultConfig = getDefaultGradient();

	computedValue.value = [...computedValue.value, defaultConfig[0]];

	// Change the active gradient
	nextTick(() => {
		const newGradientIndex = computedValue.value.length - 1;
		changeActive(newGradientIndex);
	});
}

function changeActive(index: number) {
	activeGradientIndex.value = index;
}

function changePosition(position: Position) {
	activeGradient.value = {
		...activeGradient.value,
		position: position,
	};
}

function deleteGradientValue() {
	emit('update:modelValue', null);
}
</script>

<style lang="scss">
.znpb-admin__wrapper {
	.znpb-admin__gradient-modal-wrapper {
		padding: 20px 10px;
	}

	.znpb-gradient-wrapper {
		padding: 0 10px;
	}

	.znpb-gradient-elements-wrapper {
		padding: 0;
	}
}
.znpb-gradient-wrapper {
	.znpb-gradient-elements-wrapper {
		padding: 10px 0 0;
	}
	.znpb-gradient-options-wrapper {
		.znpb-input-wrapper {
			width: 100%;
			padding: 0;
		}
	}
	.znpb-gradient-wrapper__board {
		margin-bottom: 0;
	}

	.znpb-form__input-title {
		margin-bottom: 10px;
		color: var(--zb-surface-text-color);
		font-weight: 500;
	}

	.znpb-colorpicker-add-grad {
		display: inline-flex;
		justify-content: center;
		align-items: center;
		width: 46px;
		height: 46px;
		margin-bottom: 10px;
		background: none;
		border: 2px dashed var(--zb-surface-border-color);
		border-radius: 3px;
		cursor: pointer;
		.zion-icon.zion-svg-inline {
			width: 14px;
			margin: 0 auto;
			color: var(--zb-surface-text-color);
		}
	}
	.znpb-gradient__type {
		margin-bottom: 20px;

		.znpb-tabs--minimal .znpb-tabs__header {
			padding: 3px;
			margin-bottom: 20px;
			background: var(--zb-surface-lighter-color);
			border-radius: 3px;

			& > .znpb-tabs__header-item {
				flex-grow: 1;
				padding: 10px 25px;
				border-radius: 2px;

				&:hover {
					color: var(--zb-surface-text-color);
					background-color: var(--zb-surface-lightest-color);
				}
			}

			& > .znpb-tabs__header-item--active {
				color: var(--zb-secondary-text-color);
				background-color: var(--zb-secondary-color);

				&:hover {
					color: var(--zb-secondary-text-color);
					background-color: var(--zb-secondary-color);
				}
			}
		}
	}
}

.znpb-radial-postion-wrapper {
	display: flex;

	.znpb-forms-input-wrapper {
		margin-right: 20px;

		&:last-child {
			margin-right: 0;
		}
		.znpb-forms-form__input-title {
			margin-bottom: 0;
		}
	}

	& > .znpb-input-wrapper {
		flex-direction: column;
		flex-grow: 1;
		margin-right: 10px;
		margin-bottom: 0;

		&:last-child {
			margin-right: 0;
		}
	}
}

.znpb-gradient-elements-wrapper {
	display: flex;
	flex-wrap: wrap;
	padding: 0 20px;
}

.znpb-gradient-wrapper {
	.znpb-gradient__angle {
		& > .znpb-form__input-title {
			padding: 0;
		}
	}

	.znpb-actions-overlay__actions {
		display: flex;
		align-items: center;
		width: 100%;
		padding: 8px;
		margin: 0 auto;
		font-weight: 500;
		text-align: center;
		cursor: pointer;

		span {
			&:first-child {
				margin-right: 15px;
				margin-left: 10px;
			}
		}

		.znpb-gradient__show-preset,
		.znpb-gradient__show-preset-input {
			transition: color 0.15s;

			&:hover {
				color: var(--zb-surface-text-active-color);
			}
		}
	}
}

.znpb-gradient-wrapper__delete-gradient {
	flex: 1 0 30%;
	max-width: 30px;
	margin-left: auto;
	border: 2px solid var(--zb-surface-border-color);
	border-radius: 3px;
	transition: opacity 0.15s ease;

	&:hover {
		opacity: 0.7;
	}
}

.znpb-admin-colors__container {
	display: flex;
	flex-wrap: wrap;
}
</style>
