<template>
	<LibraryElement :has-input="showPresetInput">
		<div v-if="!showPresetInput">
			<Tabs
				tab-style="minimal"
				:activeTab="activeTab"
			>
				<Tab name="Local">
					<GridColor
						@add-new-color="addLocalColor(model)"
					>
						<span
							v-for="(color,i) in localColorPatterns"
							v-bind:key="i"
							class="znpb-colorpicker-circle znpb-colorpicker-circle-color"
							:style="{ 'background-color': color }"
							@click="$emit('color-updated', color)"
						>
						</span>
					</GridColor>
				</Tab>

				<Tab name="Global">
					<div
						class="znpb-colorpicker-global-wrapper--pro"
						v-if="!isPro"
					>
						Global colors are available in
						<Label
							text="PRO"
							type="pro"
						/>
					</div>
					<GridColor
						@add-new-color="showPresetInput=!showPresetInput"
						v-else
					>
						<span
							v-for="(colorConfig,i) in globalColorPatterns"
							v-bind:key="i"
							class="znpb-colorpicker-circle znpb-colorpicker-circle-color"
							:style="{backgroundColor: colorConfig.id===selectedGlobalColor ? null : colorConfig.color}"
							@click="onGlobalColorSelected(colorConfig)"
							:class="{'znpb-colorpicker-circle--active': colorConfig.id===selectedGlobalColor}"
						>
							<span
								v-if="colorConfig.id===selectedGlobalColor"
								class="znpb-colorpicker-circle__active-bg"
							>
								<span :style="{ 'background-color': colorConfig.color }">
								</span>
							</span>

						</span>
					</GridColor>
				</Tab>
			</Tabs>
		</div>
		<PresetInput
			v-if="showPresetInput"
			@save-preset="addGlobal($event)"
			@cancel="showPresetInput=false"
		/>
	</LibraryElement>
</template>
<script>

/**
 * it emits:
 *  - the new color chosen
 */
import { computed, inject, ref } from 'vue'
import GridColor from '../Colorpicker/GridColor.vue'
import LibraryElement from '../Gradient/LibraryElement.vue'
import PresetInput from './PresetInput.vue'
import { Label } from '../Label'

export default {
	name: 'PatternContainer',
	components: {
		GridColor,
		LibraryElement,
		PresetInput,
		Label
	},
	props: {
		model: {
			type: [String, Object],
			required: false,
			default () {
				return '#000'
			}
		}
	},
	setup (props) {
		const getValueByPath = inject('getValueByPath')
		const updateValueByPath = inject('updateValueByPath')
		const schema = inject('schema')
		const showPresetInput = ref(false)

		// This should be provided by Apps that are using this component
		const useBuilderOptions = inject('builderOptions')

		const {
			addLocalColor,
			getOptionValue,
			addGlobalColor,
		} = useBuilderOptions()

		const localColors = getOptionValue('local_colors', [])
		const globalColors = getOptionValue('global_colors', [])

		let localColorPatterns = computed(() => {
			return [...localColors].reverse()
		})

		let globalColorPatterns = computed(() => {
			return [...globalColors].reverse()
		})

		let selectedGlobalColor = computed(() => {
			const { id } = schema
			const { options = {} } = getValueByPath(`__dynamic_content__.${id}`, {})
			return options.color_id
		})

		let activeTab = computed(() => {
			return selectedGlobalColor.value ? 'global' : 'local'
		})

		function addGlobal (name) {
			let globalColor = {
				id: name.split(' ').join('_'),
				color: props.model,
				name: name
			}
			showPresetInput.value = false
			addGlobalColor(globalColor)
		}

		function onGlobalColorSelected (colorConfig) {
			const { id } = schema

			updateValueByPath(`__dynamic_content__.${id}`, {
				type: 'global-color',
				options: {
					color_id: colorConfig.id
				}
			})
		}
		return {
			localColorPatterns,
			globalColorPatterns,
			onGlobalColorSelected,
			addGlobal,
			showPresetInput,
			selectedGlobalColor,
			activeTab,
			addLocalColor
		}
	},
	computed: {
		isPro () {
			if (window.ZnPbInitalData !== undefined) {
				return window.ZnPbInitalData.plugin_info.is_pro_active
			}
			if (window.ZnPbAdminPageData !== undefined) {
				return window.ZnPbAdminPageData.is_pro_active
			}
		},
	},


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
		border-color: rgb(0, 0, 0, .1);
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
