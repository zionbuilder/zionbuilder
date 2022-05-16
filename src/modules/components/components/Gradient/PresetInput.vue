<template>
	<div class="znpb-preset-input-wrapper">
		<BaseInput
			v-model="presetName"
			:placeholder="isGradient ? $translate('save_gradient_title') : $translate('add_preset_title')"
			:class="{ 'znpb-backgroundGradient__nameInput': isGradient }"
			:error="hasError"
		>
			<template v-if="isGradient" #prepend>
				<InputSelect
					v-model="gradientType"
					class="znpb-backgroundGradient__typeDropdown"
					:options="gradientTypes"
					placeholder="Type"
				/>
			</template>
			<template v-else #append>
				<Icon icon="check" @mousedown.stop="savePreset" />
				<Icon icon="close" @mousedown.prevent="$emit('cancel', true)" />
			</template>
		</BaseInput>

		<!-- Actions -->
		<template v-if="isGradient">
			<Icon icon="check" class="znpb-backgroundGradient__action" @click.stop="savePreset" />

			<Icon icon="close" class="znpb-backgroundGradient__action" @click.stop="$emit('cancel', true)" />
		</template>
	</div>
</template>

<script lang="ts">
export default {
	name: 'PresetInput',
};
</script>

<script lang="ts" setup>
import { ref, watch } from 'vue';
import { Icon } from '../Icon';
import { BaseInput } from '../BaseInput';
import { InputSelect } from '../InputSelect';
import { translate } from '@zb/i18n';

const props = withDefaults(
	defineProps<{
		isGradient?: boolean;
	}>(),
	{
		isGradient: true,
	},
);

const emit = defineEmits<{
	(e: 'save-preset', name: string, type?: string): void;
	(e: 'cancel', value: boolean): void;
}>();
const presetName = ref('');
const gradientType = ref('local');
const hasError = ref(false);
const gradientTypes = ref([
	{
		id: 'local',
		name: translate('local'),
	},
	{
		id: 'global',
		name: translate('global'),
	},
]);

function savePreset() {
	if (presetName.value.length === 0) {
		hasError.value = true;
		return;
	}

	if (props.isGradient) {
		emit('save-preset', presetName.value, gradientType.value);
	} else {
		emit('save-preset', presetName.value);
	}
}

watch(hasError, newValue => {
	let timeOut = null;
	if (newValue) {
		timeOut = setTimeout(() => {
			hasError.value = false;
		}, 500);
	}
});
</script>

<style lang="scss">
.znpb-preset-input-wrapper {
	display: flex;
	.znpb-backgroundGradient__nameInput {
		margin-right: 4px;

		& > .zion-input__suffix > .zion-input__append {
			padding-right: 0;
		}
	}

	& > .zion-input {
		input {
			max-height: 40px;
			padding: 10.5px 12px;
		}

		.zion-input__append .znpb-editor-icon-wrapper:first-child {
			margin-right: 10px;
		}
	}

	.znpb-backgroundGradient__typeDropdown {
		width: 100px;
		margin-top: -2px;
		margin-right: -2px;
		margin-bottom: -2px;
	}

	.znpb-backgroundGradient__action {
		display: flex;
		padding: 8px;
	}
}
</style>
