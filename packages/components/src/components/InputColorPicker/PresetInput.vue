<template>
	<div class="znpb-preset-input-wrapper">
		<BaseInput
			v-model="colorName"
			:placeholder="$translate('add_preset_title')"
		>
			<template v-slot:append>
				<Icon icon="check" @click.stop="$emit('save-preset', colorName)" />
				<Icon icon="close" @click.stop="$emit('cancel',true)"/>
			</template>
		</BaseInput>
	</div>
</template>
<script>
import { Icon } from '../Icon'
import BaseInput from '../forms/elements/input/BaseInput.vue'
/**
 * it emits:
 *  - the new color chosen
 */
export default {
	name: 'PresetInput',
	components: {
		BaseInput,
		Icon
	},
	props: {
		modelValue: {
			type: String,
			required: false
		}
	},
	data () {
		return {
			colorName: null
		}
	}
}
</script>
<style lang="scss">
.znpb-preset-input-wrapper {
	display: flex;
	align-items: center;
	width: 100%;
	background: transparent;
	.zion-input {
		input {
			max-height: 40px;
			padding: 10.5px 12px;
		}

		&__append .znpb-editor-icon-wrapper:first-child {
			margin-right: 10px;
		}
	}
}

</style>
