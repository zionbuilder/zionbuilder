<template>
	<LibraryElement :has-input="showPresetInput">
		<div v-if="!showPresetInput">
			<Tabs tab-style="minimal" :active-tab="activeTab">
				<Tab name="Local">
					<GridColor @add-new-color="addLocalColor(model)">
						<span
							v-for="(color, i) in localColorPatterns"
							:key="i"
							class="znpb-colorpicker-circle znpb-colorpicker-circle-color"
							:style="{ 'background-color': color }"
							@click="emit('color-updated', color)"
						>
						</span>
					</GridColor>
				</Tab>

				<Tab name="Global">
					<div v-if="!isPro" class="znpb-colorpicker-global-wrapper--pro">
						Global colors are available in
						<Label text="PRO" type="pro" />
					</div>
					<GridColor v-else @add-new-color="showPresetInput = !showPresetInput">
						<span
							v-for="(colorConfig, i) in globalColorPatterns"
							:key="i"
							class="znpb-colorpicker-circle znpb-colorpicker-circle-color"
							:style="{ backgroundColor: colorConfig.id === selectedGlobalColor ? '' : colorConfig.color }"
							:class="{ 'znpb-colorpicker-circle--active': colorConfig.id === selectedGlobalColor }"
							@click.stop="onGlobalColorSelected(colorConfig)"
						>
							<span v-if="colorConfig.id === selectedGlobalColor" class="znpb-colorpicker-circle__active-bg">
								<span :style="{ 'background-color': colorConfig.color }"> </span>
							</span>
						</span>
					</GridColor>
				</Tab>
			</Tabs>
		</div>
		<PresetInput
			v-if="showPresetInput"
			:is-gradient="false"
			@save-preset="addGlobal($event)"
			@cancel="showPresetInput = false"
		/>
	</LibraryElement>
</template>

<script lang="ts">
export default {
	name: 'PatternContainer',
};
</script>

<script lang="ts" setup>
import { computed, inject, ref } from 'vue';
import GridColor from '../Colorpicker/GridColor.vue';
import LibraryElement from '../Gradient/LibraryElement.vue';
import PresetInput from '../Gradient/PresetInput.vue';
import { Label } from '../Label';
import { useBuilderOptionsStore } from '../../store';

type GlobalColor = { color: string; id: string; name: string };

const props = withDefaults(
	defineProps<{
		model?: string;
	}>(),
	{
		model: '#000',
	},
);

const emit = defineEmits<{
	(e: 'color-updated', value: string): void;
}>();

// @Todo add proper TS type to injections
const formApi = inject('OptionsForm');
const getValueByPath = inject('getValueByPath');
const schema: Record<string, any> = inject('schema', {});

const { addLocalColor, getOptionValue, addGlobalColor } = useBuilderOptionsStore();

const localColors: string[] = getOptionValue('local_colors', []);
const globalColors: GlobalColor[] = getOptionValue('global_colors', []);

const showPresetInput = ref(false);

const isPro = computed(() => {
	if (window.ZBCommonData !== undefined) {
		return window.ZBCommonData.is_pro_active;
	}

	return false;
});

const localColorPatterns = computed(() => {
	return [...localColors].reverse();
});

const globalColorPatterns = computed(() => {
	return [...globalColors].reverse();
});

const selectedGlobalColor = computed(() => {
	const { id = '' }: { id?: string } = schema;
	const { options = {} } = getValueByPath(`__dynamic_content__.${id}`, {});
	return options.color_id;
});

const activeTab = computed(() => {
	return selectedGlobalColor.value ? 'global' : 'local';
});

function addGlobal(name: string) {
	const globalColor = {
		id: name.split(' ').join('_'),
		color: props.model,
		name: name,
	};
	showPresetInput.value = false;
	addGlobalColor(globalColor);
}

function onGlobalColorSelected(colorConfig: GlobalColor) {
	const { id } = schema;

	formApi.updateValueByPath(`__dynamic_content__.${id}`, {
		type: 'global-color',
		options: {
			color_id: colorConfig.id,
		},
	});
}
</script>

<style lang="scss">
.znpb-colorpicker-circle {
	&__active-bg {
		position: absolute;
		top: 0;
		left: 0;
		width: 18px;
		height: 18px;
		background-color: rgba(50, 193, 121, 0);
		border-color: rgb(0, 0, 0, 0.1);
		border-style: solid;
		border-width: 1px;
		border-radius: 2px;
		& > span {
			position: absolute;
			top: 2px;
			left: 2px;
			width: 24px;
			height: 24px;
			border-radius: 50%;
		}
	}
	&.znpb-colorpicker-circle--active {
		position: relative;
	}
}

.znpb-colorpicker-global-wrapper--pro {
	padding: 10px;
	text-align: center;
}
</style>
