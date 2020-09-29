<template>
	<div class="znpb-link-wrapper">
		<InputWrapper layout="full">
			<BaseInput
				v-model="link"
				:placeholder="$translate('add_an_url')"
				:title="$translate('link_url')"
			>
				<BaseIcon
					slot="prepend"
					icon="link"
				></BaseIcon>
			</BaseInput>
		</InputWrapper>
		<InputWrapper
			layout="inline"
			:schema="schema"
		>
			<InputSelect
				:options="targetOptions"
				v-model="target"
				:title="$translate('link_target')"
			></InputSelect>
		</InputWrapper>
		<InputWrapper
			layout="inline"
			:schema="schema"
		>
			<BaseInput
				v-model="title"
				:clearable="false"
				:title="$translate('link_title')"
				:placeholder="$translate('set_a_title')"
			/>
		</InputWrapper>
	</div>
</template>

<script>
import { BaseInput, InputSelect, InputWrapper } from '@zb/components/forms'

export default {
	name: 'Link',
	components: {
		BaseInput,
		InputSelect,
		InputWrapper
	},
	props: {
		modelValue: {
			default () {
				return {}
			}
		}
	},
	data () {
		return {
			targetOptions: [
				{
					id: '_blank',
					name: this.$translate('link_new_window')
				},
				{
					id: '_self',
					name: this.$translate('link_blank')
				}
			]
		}
	},
	computed: {
		link: {
			get () {
				return this.modelValue && this.modelValue['link'] ? this.modelValue['link'] : ''
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					'link': newValue
				})
			}
		},
		target: {
			get () {
				return this.modelValue && this.modelValue['target'] ? this.modelValue['target'] : '_self'
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					'target': newValue
				})
			}
		},
		title: {
			get () {
				return this.modelValue && this.modelValue['title'] ? this.modelValue['title'] : ''
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					'title': newValue
				})
			}
		},
		schema () {
			return {
				width: 50
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-link-wrapper {
	display: flex;
	flex-wrap: wrap;
	margin: 0 -5px;

	& > .znpb-input-wrapper {
		&:first-child {
			padding-bottom: 10px;
		}

		& > .znpb-forms-input-content {
			display: flex;

			label {
				margin-right: 10px;

				&:last-child {
					margin-right: 0;
				}
			}
		}

		& .zion-input__prepend {
			background: $surface-variant;
		}
	}

	& > .zion-input {
		margin-bottom: 15px;
	}
}
</style>
