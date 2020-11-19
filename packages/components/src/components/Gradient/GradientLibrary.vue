<template>
	<LibraryElement
		:animation='false'
		icon="close"
		@close-library="$emit('close-library')"
	>
		<Tabs tab-style="minimal">
			<Tab name="Local">
				<div class=" znpb-form-library-grid__panel-content znpb-fancy-scrollbar">
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
					<div class="znpb-form-library-grid__panel-content znpb-fancy-scrollbar">
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
/**
 * it receives no aimation on beggining
 * it emits:
 *  - the new color chosen
 */
import GradientPreview from './GradientPreview.vue'
import LibraryElement from './LibraryElement.vue'
import { Label } from '../Label'
import { useAdminData } from '@zionbuilder/composables'
import { computed, inject } from 'vue'
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
		const $zb = inject('$zb')
		const getValueByPath = inject('getValueByPath')
		const updateValueByPath = inject('updateValueByPath')
		const schema = inject('schema')
		const { adminData } = useAdminData()
		let isPro = adminData.value.is_pro_active
		const getGlobalGradients = computed(() => {
			return $zb.options.getOptionValue('global_gradients')
		})

		const getLocalGradients = computed(() => {
			return $zb.options.getOptionValue('local_gradients')
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
</style>
