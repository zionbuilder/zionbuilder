<template>

	<div class="znpb-tour-modal__content">
		<Icon
			icon="close"
			class="znpb-tour-modal__header-icon-close"
			@click="$emit('end-tour',true)"
		/>
		<span class="znpb-tour-modal__title">{{step.title}}</span>
		<div
			class="znpb-tour-modal__description"
			v-html="step.description"
		></div>

		<div class="znpb-tour-modal__actions">
			<Button
				type="line"
				@click="$emit('end-tour',true)"
				v-html="$translate('end_tour')"
			/>

			<Button
				v-if="!isLastStep"
				:type="!isPreviewLoading ? 'secondary' : 'disabled'"
				@click="moveToStep"
				v-html="buttonText"
				class="znpb-tour-modal__actions-next"
			>
			</Button>

		</div>
	</div>

</template>

<script>
import { mapGetters } from 'vuex'

export default {
	name: 'ModalStep',
	props: {
		step: {
			type: Object
		},
		isLastStep: {
			type: Boolean,
			required: false,
			default: false
		}
	},

	computed: {
		...mapGetters([
			'isPreviewLoading'
		]),
		buttonText () {
			if (this.isPreviewLoading) {
				return `<div class="znpb-admin-small-loader"></div> ${this.$translate('take_tour')}`
			} else return !this.step.selector ? this.$translate('take_tour') : this.$translate('next_step')
		},
		prevButtonText () {
			return this.$translate('previous_step')
		}
	},
	methods: {
		moveToStep () {
			this.$emit('next-step', true)
		}
	}
}
</script>

<style lang="scss">
.znpb-tour-modal {
	&__description {
		margin-bottom: 30px;
		font-family: $font-stack;
		line-height: 24px;
	}
	&__header-icon-close {
		@include circlesimple(30px);
		position: absolute;
		top: 22px;
		right: 30px;
		color: $surface-headings-color;
		background: $surface-variant;
		cursor: pointer;
		&:hover {
			background: darken($surface-variant, 10%);
		}
	}

	&__title {
		display: block;
		margin-bottom: 30px;
		font-family: $font-stack;
		font-size: 16px;
		font-weight: 500;
	}

	&__actions {
		display: flex;
		justify-content: center;

		.znpb-button {
			margin-right: 10px;
			text-transform: capitalize;

			&:last-of-type {
				position: relative;
				margin-right: 0;
				.znpb-admin-small-loader.znpb-admin-small-loader {
					left: 24px;
				}
			}
		}
	}
}
</style>
