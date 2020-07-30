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
						@click.native="$emit('activate-gradient',gradient.config)"
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
							@click.native="onGlobalGradientSelected(gradient)"
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
import { mapGetters, mapActions } from 'vuex'
import GradientPreview from '@/common/components/gradient/GradientPreview.vue'
import LibraryElement from './LibraryElement.vue'
import Label from '@/editor/common/Label'

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
	data () {
		return {
			onstart: true,
			expand: false
		}
	},
	computed: {
		...mapGetters([
			'getLocalGradients',
			'getGlobalGradients',
			'isPro'
		])

	},
	methods: {
		onGlobalGradientSelected (gradient) {
			const { id } = this.inputWrapper.schema

			this.optionsForm.updateValueByPath(`__dynamic_content__.${id}`, {
				type: 'global-gradient',
				options: {
					gradient_id: gradient.id
				}
			})

			// Delete the saved value
			this.$nextTick(() => {
				this.$emit('activate-gradient', null)
			})
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
