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
						:config="gradient"
						:round="true"
						@click.native="$emit('activate-gradient',gradient)"
					/>
				</div>
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
				<template v-else>
					<div class="znpb-form-library-grid__panel-content znpb-fancy-scrollbar">
						<GradientPreview
							v-for="(gradient,i) in getArrayGradients"
							v-bind:key="i"
							:config="gradient"
							:round="true"
							@click.native="$emit('activate-gradient',gradient)"
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
	props: {
		model: {
			type: [String, Object],
			required: false
		}
	},
	data () {
		return {
			onstart: true,
			expand: false,
			loaded: false
		}
	},
	computed: {
		...mapGetters([
			'getLocalGradients',
			'getGlobalGradients',
			'isPro'
		]),
		getArrayGradients () {
			let newArray = []
			this.getGlobalGradients.forEach((item) => {
				let gradientName = Object.keys(item)
				let newObject = item[gradientName]

				newArray.push(Object.values(newObject))
			})
			return newArray
		}
	},
	methods: {
		...mapActions([
			'fetchOptionsOnce'
		])
	},
	created () {
		this.fetchOptionsOnce()
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
