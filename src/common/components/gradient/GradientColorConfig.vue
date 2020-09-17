<template>

	<div class="znpb-gradient-actions">
		<OptionsForm
			:schema="schema"
			v-model="valueModel"
			class="znpb-gradient-color-form"
		/>
		<div
			class="znpb-gradient-actions__delete"
			v-if="showDelete"
		>
			<BaseIcon
				icon="close"
				@click.stop.native="$emit('delete-color', config)"
				class="znpb-gradient-actions-delete"
			/>
		</div>
	</div>

</template>
<script>
import BaseIcon from '@/common/components/BaseIcon.vue'

export default {
	name: 'GradientColorConfig',
	components: {
		BaseIcon
	},
	props: {
		config: {
			type: Object,
			required: true
		},
		showDelete: {
			type: Boolean,
			required: false,
			default: true
		}
	},
	computed: {
		valueModel: {
			get () {
				return this.config
			},
			set (newValue) {
				this.$emit('input', newValue)
			}
		},
		schema () {
			return {
				color: {
					type: 'colorpicker',
					id: 'color',
					width: '50'
				},
				position: {
					type: 'number',
					id: 'position',
					content: '%',
					width: '50',
					min: 0,
					max: 100
				}
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-gradient-actions {
	position: relative;
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 10px;

	&:last-child {
		margin-bottom: 0;
	}

	.znpb-gradient-color-form {
		flex-wrap: nowrap;
		justify-content: space-between;
		padding: 0;
		.znpb-input-type--colorpicker {
			margin-right: 5px;

			&:last-child {
				margin-right: 0;
			}
		}
	}

	.znpb-form-colorpicker-trigger-wrapper {
		padding-left: 8px;
		margin-bottom: 0;
	}
	.znpb-form-colorpicker__text {
		max-width: 80px;
	}
	.znpb-form-gradient {
		display: flex;
		align-items: center;
		margin: 0;

		&:first-child {
			flex-basis: 50%;
			flex-grow: 1;
			flex-shrink: 0;
			margin-right: 10px;
		}

		.znpb-forms-input-content {
			width: 100%;
		}
	}
	& > .znpb-input-wrapper > .znpb-form__input-title {
		padding: 15px 15px 15px 0;
		margin-bottom: 0;
	}
	&__delete {
		display: flex;
		justify-content: flex-end;
		align-items: center;
		padding: 11px;
		margin-left: 5px;
		font-size: 14px;
		background: transparent;
		border: 2px solid #e5e5e5;
		border-radius: 3px;
		transition: opacity .15s ease;
		cursor: pointer;

		&:hover {
			opacity: .7;
		}
	}

	.znpb-form-gradient + .znpb-gradient-actions__delete {
		margin-left: 10px;
	}
}
</style>
