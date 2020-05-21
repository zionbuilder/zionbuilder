<template>
	<div class="znpb-preset-input-wrapper">
		<BaseInput
			v-model="colorName"
			:placeholder="$translate('add_preset_title')"
		>
			<template slot="append">
				<BaseIcon icon="check" @click.stop.native="$emit('save-preset', colorName)" />
				<BaseIcon icon="close" @click.stop.native="$emit('cancel',true)"/>
			</template>
		</BaseInput>
	</div>
</template>
<script>
import BaseInput from '@/common/components/forms/elements/input/BaseInput.vue'
/**
 * it emits:
 *  - the new color chosen
 */
export default {
	name: 'PresetInput',
	components: {
		BaseInput
	},
	props: {
		value: {
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

.znpb-gradient-wrapper .znpb-preset-input-wrapper {
	margin-right: 10px;
}

</style>
