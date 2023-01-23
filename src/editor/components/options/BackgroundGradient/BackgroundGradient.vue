<template>
	<div class="znpb-style-background-gradient">
		<EmptyList v-if="!modelValue && !showLibrary" class="znpb-style-background-gradient__empty" :no-margin="true">
			<a @click="addNewGradient">{{ i18n.__('Add new background gradient', 'zionbuilder') }}</a>
			<template v-if="hasLibrary">
				<div>{{ i18n.__('or', 'zionbuilder') }}</div>
				<a @click="showLibrary = true">{{ i18n.__('Select from library', 'zionbuilder') }}</a>
			</template>
		</EmptyList>

		<GradientGenerator v-if="modelValue" ref="gradientGenerator" v-model="gradientModel" />

		<GradientLibrary
			v-if="showLibrary"
			@close-library="showLibrary = false"
			@activate-gradient="(gradientModel = $event), (showLibrary = false)"
		/>
	</div>
</template>

<script lang="ts" setup>
import * as i18n from '@wordpress/i18n';
import { ref, computed } from 'vue';
import { getDefaultGradient } from '@zb/utils';

const props = withDefaults(
	defineProps<{
		modelValue?: Array<any> | null;
		hasLibrary?: boolean;
	}>(),
	{
		modelValue: null,
		hasLibrary: true,
	},
);

const emit = defineEmits(['update:modelValue']);

// Refs
const showLibrary = ref(false);

// computed
const gradientModel = computed({
	get() {
		return props.modelValue || null;
	},
	set(newGradient) {
		emit('update:modelValue', newGradient);
	},
});

// methods
function addNewGradient() {
	gradientModel.value = getDefaultGradient();
}
</script>

<style lang="scss">
.znpb-style-background-gradient {
	margin-bottom: 20px;

	&__empty {
		display: block;
		width: 100%;
		line-height: 22px;

		a {
			color: var(--zb-secondary-color);
		}
	}
	.znpb-empty-list__content {
		padding: 50px 30px;
	}
}
</style>
