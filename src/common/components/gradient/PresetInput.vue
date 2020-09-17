<template>
	<div
		class="znpb-preset-input-wrapper"
	>
		<BaseInput
			v-model="gradientName"
			:placeholder="$translate('save_gradient_title')"
			class="znpb-backgroundGradient__nameInput"
			:error="hasError"
		>
			<template slot="append">
				<InputSelect
					slot="prepend"
					class="znpb-backgroundGradient__typeDropdown"
					:options="gradientTypes"
					placeholder="Type"
					v-model="gradientType"
				/>

			</template>
		</BaseInput>

		<!-- Actions -->
		<BaseIcon
			icon="check"
			class="znpb-backgroundGradient__action"
			@click.stop.native="saveGradient"
		/>

		<BaseIcon
			icon="close"
			class="znpb-backgroundGradient__action"
			@click.stop.native="$emit('cancel',true)"
		/>
	</div>
</template>
<script>
import BaseIcon from '@/common/components/BaseIcon.vue'
import { BaseInput, InputSelect } from '@/common/components/forms'
/**
 * it emits:
 *  - the new color chosen
 */
export default {
	name: 'PresetInput',
	components: {
		BaseInput,
		InputSelect,
		BaseIcon
	},
	data () {
		return {
			gradientName: '',
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
		saveGradient () {
			if (this.gradientName.length === 0) {
				this.hasError = true
				return
			}

			this.$emit('save-preset', this.gradientName, this.gradientType)
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
	.znpb-backgroundGradient__nameInput {
		margin-right: 4px;

		& > .zion-input__suffix > .zion-input__append {
			padding-right: 0;
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
