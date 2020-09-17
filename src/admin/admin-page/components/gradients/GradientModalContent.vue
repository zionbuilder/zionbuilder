<template>
	<modal
		:show.sync="showModal"
		:width="360"
		:title="$translate('gradients')"
		append-to="#znpb-admin"
		@close-modal="onModalClose"
	>
		<div class="znpb-admin__gradient-modal-wrapper znpb-fancy-scrollbar">
			<GradientGenerator
				v-model="gradientValue"
				@updated-gradient="$emit('updated-gradient', $event)"
				:save-to-library="false"
			/>
		</div>

	</modal>
</template>
<script>
import { GradientGenerator } from '@zb/components'

export default {
	name: 'GradientModalContent',
	components: {
		GradientGenerator
	},
	props: {
		show: {
			type: Boolean,
			required: true
		},
		gradient: {
			type: Array,
			required: false
		}
	},
	data () {
		return {
			gradientConfig: this.config
		}
	},
	computed: {
		showModal: {
			get () {
				return this.show
			},
			set (newValue) {
				this.$emit('update:show', newValue)
			}
		},
		gradientValue: {
			get () {
				return this.gradient
			},
			set (newValue) {
				this.$emit('update-gradient', newValue)
			}
		}
	},
	methods: {
		onModalClose () {
			this.$emit('save-gradient', this.gradientConfig)
		}
	}
}
</script>
<style lang="scss">

.znpb-admin__gradient-modal-wrapper {
	overflow: auto;
	min-width: 360px;
	.znpb-toggle-switch {
		input[type=radio]:checked:before {
			display: none;
		}
	}
}
</style>
