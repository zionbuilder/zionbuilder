<template>
	<div
		class="znpb-style-background-gradient"
	>
		<EmptyList
			class="znpb-style-background-gradient__empty"
			v-if="!value && !showLibrary"
			:no-margin="true"
		>
			<a @click="addNewGradient">{{$translate('add_background_gradient')}}</a>
			<template v-if="hasLibrary">
				<div>{{$translate('or')}}</div>
				<a @click="showLibrary = true">{{$translate('select_background_gradient')}}</a>
			</template>
		</EmptyList>

		<GradientGenerator
			v-if="value"
			v-model="gradientModel"
			ref="gradientGenerator"
		/>

		<GradientLibrary
			v-if="showLibrary"
			@close-library="showLibrary=false"
			@activate-gradient="gradientModel = $event, showLibrary=false"
		/>
	</div>
</template>

<script>
import { EmptyList } from '@zb/components/forms'
import { GradientGenerator, GradientLibrary } from '@zb/components'
import { getDefaultGradientConfig } from '@zb/utils'

export default {
	name: 'BackgroundGradient',
	components: {
		GradientGenerator,
		EmptyList,
		GradientLibrary
	},
	props: {
		value: {
			type: Array,
			required: false
		},
		hasLibrary: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	data () {
		return {
			showLibrary: false
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
	methods: {
		addNewGradient () {
			this.gradientModel = getDefaultGradientConfig()
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
		line-height: 22px;

		a {
			color: $secondary;
		}
	}
	.znpb-empty-list__content {
		padding: 50px 30px;
	}
}
</style>
