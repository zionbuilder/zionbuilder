<template>
	<LibraryElement
		:animation='false'
		icon="close"
		@close-library="$emit('close-library')"
	>
		<Tabs tab-style="minimal">
			<Tab name="Local">
				<div
					v-if="getLocalGradients.length===0"
					class="znpb-form-library-grid__panel-content-message"
				>{{$translate('no_local_gradients')}}</div>
				<div
					v-else
					class="znpb-form-library-grid__panel-content znpb-form-library-grid__panel-content--no-pd znpb-fancy-scrollbar"
				>
					<GradientPreview
						v-for="(gradient,i) in getLocalGradients"
						v-bind:key="i"
						:config="gradient.config"
						:round="true"
						@click="$emit('activate-gradient',gradient.config)"
					/>
				</div>
			</Tab>
			<Tab name="Global">
				<div
					class="znpb-colorpicker-global-wrapper--pro"
					v-if="!isPro"
				>

					{{$translate('global_colors_availability')}}

					<Label
						:text="$translate('pro')"
						type="pro"
					/>
				</div>
				<template v-else>
					<div
						v-if="getGlobalGradients.length===0"
						class="znpb-form-library-grid__panel-content-message"
					>{{$translate('no_global_gradients')}}</div>
					<div
						v-else
						class="znpb-form-library-grid__panel-content znpb-form-library-grid__panel-content--no-pd znpb-fancy-scrollbar"
					>
						<GradientPreview
							v-for="(gradient,i) in getGlobalGradients"
							v-bind:key="i"
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
<script>
import { computed, inject, nextTick } from 'vue'

// Components
import GradientPreview from './GradientPreview.vue'
import LibraryElement from './LibraryElement.vue'
import { Label } from '../Label'

export default {
	name: 'GradientLibrary',
	components: {
		LibraryElement,
		GradientPreview,
		Label
	},
	inject: ['inputWrapper', 'optionsForm'],
	props: {
		model: {
			type: [String, Object],
			required: false
		}
	},
	setup (props, { emit }) {
		function getPro () {
			if (window.ZnPbComponentsData !== undefined) {
				return window.ZnPbComponentsData.is_pro_active
			}

			return false
		}

		let isPro = getPro()
		const getValueByPath = inject('getValueByPath')
		const updateValueByPath = inject('updateValueByPath')

		const schema = inject('schema')

		// This should be provided by Apps that are using this component
		const useBuilderOptions = inject('builderOptions')
		const {
			getOptionValue
		} = useBuilderOptions()

		const getGlobalGradients = computed(() => {
			return getOptionValue('global_gradients')
		})

		const getLocalGradients = computed(() => {
			return getOptionValue('local_gradients')
		})

		function onGlobalGradientSelected (gradient) {
			const { id } = schema
			updateValueByPath(`__dynamic_content__.${id}`, {
				type: 'global-gradient',
				options: {
					gradient_id: gradient.id
				}
			})

			// Delete the saved value
			nextTick(() => {
				emit('activate-gradient', null)
			})
		}

		return {
			isPro,
			getGlobalGradients,
			getLocalGradients,
			onGlobalGradientSelected
		}
	}
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
