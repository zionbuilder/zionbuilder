<template>
	<div class="znpb-style-background-gradient">
		<EmptyList v-if="!modelValue && !showLibrary" class="znpb-style-background-gradient__empty" :no-margin="true">
			<a @click="addNewGradient">{{ $translate('add_background_gradient') }}</a>
			<template v-if="hasLibrary">
				<div>{{ $translate('or') }}</div>
				<a @click="showLibrary = true">{{ $translate('select_background_gradient') }}</a>
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

<script>
import { getDefaultGradient } from '/@/common/utils';
export default {
	name: 'BackgroundGradient',
	props: {
		modelValue: {
			type: Array,
			required: false,
		},
		hasLibrary: {
			type: Boolean,
			required: false,
			default: true,
		},
	},
	data() {
		return {
			showLibrary: false,
		};
	},
	computed: {
		gradientModel: {
			get() {
				return this.modelValue || null;
			},
			set(newGradient) {
				this.$emit('update:modelValue', newGradient);
			},
		},
	},
	methods: {
		addNewGradient() {
			this.gradientModel = getDefaultGradient();
		},
	},
};
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
