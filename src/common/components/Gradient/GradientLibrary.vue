<template>
	<LibraryElement :animation="false" icon="close" @close-library="$emit('close-library')">
		<Tabs tab-style="minimal">
			<Tab name="Local">
				<div v-if="getLocalGradients.length === 0" class="znpb-form-library-grid__panel-content-message">
					{{ $translate('no_local_gradients') }}
				</div>
				<div
					v-else
					class="znpb-form-library-grid__panel-content znpb-form-library-grid__panel-content--no-pd znpb-fancy-scrollbar"
				>
					<GradientPreview
						v-for="(gradient, i) in getLocalGradients"
						:key="i"
						:config="gradient.config"
						:round="true"
						@click="$emit('activate-gradient', gradient.config)"
					/>
				</div>
			</Tab>
			<Tab name="Global">
				<div v-if="!isPro" class="znpb-colorpicker-global-wrapper--pro">
					{{ $translate('global_colors_availability') }}

					<Label :text="$translate('pro')" type="pro" />
				</div>
				<template v-else>
					<div v-if="getGlobalGradients.length === 0" class="znpb-form-library-grid__panel-content-message">
						{{ $translate('no_global_gradients') }}
					</div>
					<div
						v-else
						class="znpb-form-library-grid__panel-content znpb-form-library-grid__panel-content--no-pd znpb-fancy-scrollbar"
					>
						<GradientPreview
							v-for="(gradient, i) in getGlobalGradients"
							:key="i"
							:config="gradient.config"
							:round="true"
							@click="onGlobalGradientSelected(gradient)"
						/>
					</div>
				</template>
			</Tab>
		</Tabs>
	</LibraryElement>
</template>

<script lang="ts">
export default {
	name: 'GradientLibrary',
};
</script>

<script lang="ts" setup>
import { computed, inject, nextTick } from 'vue';
import GradientPreview from './GradientPreview.vue';
import LibraryElement from './LibraryElement.vue';
import { Label } from '../Label';
import type { Gradient } from './GradientBar.vue';
import { useBuilderOptionsStore } from '../../store';

interface GradientModel {
	id: string;
	name: string;
	config: Gradient[];
}

defineProps<{
	model?: GradientModel[];
}>();

const emit = defineEmits<{
	(e: 'activate-gradient', value: Gradient[] | null): void;
	(e: 'close-library'): void;
}>();

const updateValueByPath = inject('updateValueByPath');

function getPro() {
	if (window.ZBCommonData !== undefined) {
		return window.ZBCommonData.is_pro_active;
	}

	return false;
}

const isPro = getPro();

const schema = inject('schema');

// This should be provided by Apps that are using this component
const { getOptionValue } = useBuilderOptionsStore();

const getGlobalGradients = computed<GradientModel[]>(() => {
	return getOptionValue('global_gradients');
});

const getLocalGradients = computed<GradientModel[]>(() => {
	return getOptionValue('local_gradients');
});

function onGlobalGradientSelected(gradient: GradientModel) {
	const { id } = schema;
	updateValueByPath(`__dynamic_content__.${id}`, {
		type: 'global-gradient',
		options: {
			gradient_id: gradient.id,
		},
	});

	// Delete the saved value
	nextTick(() => {
		emit('activate-gradient', null);
	});
}
</script>
<style lang="scss">
.znpb-form-library-inner-pattern-wrapper {
	.gradient-type-rounded {
		width: 40px;
		height: 40px;
		margin-bottom: 0;
	}
}
.znpb-form-library-grid__panel-content-message {
	padding: 10px;
	text-align: center;
}
</style>
