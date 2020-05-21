<template>
	<div
		class="znpb-style-background-gradient"
		@click="showGradient = true"
	>
		<EmptyList
			class="znpb-style-background-gradient__empty"
			v-if="!showGradient && !value"
			:no-margin="true"
		>
			{{$translate('select_background_gradient')}}
		</EmptyList>
		<GradientGenerator
			v-if="showGradient || value"
			:has-library="true"
			v-model="gradientModel"
			ref="gradientGenerator"
		/>
	</div>
</template>

<script>
import GradientGenerator from '@/common/components/gradient/GradientGenerator.vue'
import { EmptyList } from '@/common/components/forms'
export default {
	name: 'BackgroundGradient',
	components: {
		GradientGenerator,
		EmptyList
	},
	props: {
		value: {
			type: Array,
			required: false

		}
	},
	data () {
		return {
			showGradient: false
		}
	},
	computed: {
		gradientModel: {
			get () {
				return this.value || null
			},
			set (newGradient) {
				this.$emit('input', newGradient)
			}
		}
	},
	watch: {
		value (newValue) {
			if (!newValue) {
				this.showGradient = false
			}
		},
		showGradient (newValue) {
			if (newValue && !this.value) {
				this.$nextTick(() => {
					this.$emit('input', this.$refs.gradientGenerator.getValue())
				})
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-style-background-gradient {
	margin-bottom: 20px;
	&__empty {
		display: block;
		width: 100%;
	}
	.znpb-empty-list__content {
		padding: 50px 30px;
	}
}
</style>
