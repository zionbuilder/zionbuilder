<template>
	<Modal
		v-model:show="showModal"
		:width="360"
		:title="i18n.__('Gradients', 'zionbuilder')"
		append-to="#znpb-admin"
		@close-modal="onModalClose"
	>
		<div class="znpb-admin__gradient-modal-wrapper znpb-fancy-scrollbar">
			<GradientGenerator
				v-model="gradientValue"
				:save-to-library="false"
				@updated-gradient="emit('update-gradient', $event)"
			/>
		</div>
	</Modal>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref, computed } from 'vue';

const props = withDefaults(
	defineProps<{
		show: boolean;
		gradient: string[];
	}>(),
	{
		show: false,
		gradient: () => [],
	},
);
const emit = defineEmits(['update:show', 'update-gradient', 'save-gradient']);

const gradientConfig = ref(props.gradient);
const showModal = computed({
	get() {
		return props.show;
	},
	set(newValue) {
		emit('update:show', newValue);
	},
});

const gradientValue = computed({
	get() {
		return props.gradient;
	},
	set(newValue) {
		emit('update-gradient', newValue);
	},
});

function onModalClose() {
	emit('save-gradient', gradientConfig.value);
}
</script>
<style lang="scss">
.znpb-admin__gradient-modal-wrapper {
	overflow: auto;
	min-width: 360px;
	padding: 20px;

	.znpb-toggle-switch {
		input[type='radio']:checked:before {
			display: none;
		}
	}
}
</style>
