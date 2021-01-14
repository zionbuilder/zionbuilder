<template>
	<div class="znpb-preset-input-wrapper">
		<BaseInput
			v-model="presetName"
			:placeholder="isGradient? $translate('save_gradient_title'):$translate('add_preset_title')"
			:class="{'znpb-backgroundGradient__nameInput':isGradient}"
			:error="hasError"
		>

			<template
				v-slot:prepend
				v-if="isGradient"
			>
				<InputSelect
					class="znpb-backgroundGradient__typeDropdown"
					:options="gradientTypes"
					placeholder="Type"
					v-model="gradientType"
				/>

			</template>
			<template
				v-slot:append
				v-else
			>
				<Icon
					icon="check"
					@mousedown.stop="savePreset"
				/>
				<Icon
					icon="close"
					@mousedown.prevent="$emit('cancel',true)"
				/>
			</template>
		</BaseInput>

		<!-- Actions -->
		<template v-if="isGradient">
			<Icon
				icon="check"
				class="znpb-backgroundGradient__action"
				@click.stop="savePreset"
			/>

			<Icon
				icon="close"
				class="znpb-backgroundGradient__action"
				@click.stop="$emit('cancel',true)"
			/>
		</template>
	</div>
</template>
<script>
import { Icon } from '../Icon'
import { BaseInput } from '../BaseInput'
import { InputSelect } from '../InputSelect'

/**
 * it emits:
 *  - the new color chosen
 */
export default {
	name: 'PresetInput',
	components: {
		BaseInput,
		InputSelect,
		Icon
	},
	props: {
		isGradient: {
			type: Boolean,
			default: true
		}
	},
	data () {
		return {
			presetName: '',
			gradientType: 'local',
			hasError: false,
			gradientTypes: [
				{
					id: 'local',
					name: this.$translate('local')
				},
				{
					id: 'global',
					name: this.$translate('global')
				}
			]
		}
	},
	methods: {
		savePreset () {

			if (this.presetName.length === 0) {
				this.hasError = true
				return
			}

			if (this.isGradient) {
				this.$emit('save-preset', this.presetName, this.gradientType)
			} else this.$emit('save-preset', this.presetName)

		}
	},
	watch: {
		hasError (newValue) {
			let timeOut = null
			if (newValue) {
				timeOut = setTimeout(() => {
					this.hasError = false
				}, 500)
			} else {
				clearTimeout(timeOut)
				this.hasError = false
			}
		}
	}
}
</script>
<style lang="scss">
.znpb-preset-input-wrapper {
	display: flex;
	.znpb-backgroundGradient__nameInput {
		margin-right: 4px;

		& > .zion-input__suffix > .zion-input__append {
			padding-right: 0;
		}
	}

	& > .zion-input {
		input {
			max-height: 40px;
			padding: 10.5px 12px;
		}

		.zion-input__append .znpb-editor-icon-wrapper:first-child {
			margin-right: 10px;
		}
	}

	.znpb-backgroundGradient__typeDropdown {
		width: 100px;
		margin-top: -2px;
		margin-right: -2px;
		margin-bottom: -2px;
	}

	.znpb-backgroundGradient__action {
		display: flex;
		padding: 8px;
	}
}
</style>
