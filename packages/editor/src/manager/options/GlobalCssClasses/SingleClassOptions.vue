<template>
	<div class="znpb-single-class-wrapper">
		<InputWrapper
			class="znpb-input-wrapper--class-styling"
			title="Class name"
			description="change class name"
		>
			<BaseInput
				v-model="classTitle"
				:clearable="true"
			/>

			<PseudoSelectors v-model="getStyleOptionsModel" />

		</InputWrapper>

		<OptionsForm
			:schema="getElementStyleOptionsSchema"
			v-model="getStyleOptionsModel"
			class="znpb-single-class-wrapper__accordions"
		/>
	</div>
</template>
<script>
import { mapGetters } from 'vuex'
import { BaseInput, InputWrapper } from '@zb/components/forms'
import PseudoSelectors from '../../../components/elementOptions/PseudoSelectors.vue'
export default {
	name: 'SingleClassOptions',
	components: {
		BaseInput,
		InputWrapper,
		PseudoSelectors
	},
	props: {
		classItem: {
			type: Object,
			required: false
		},
		activeProp: {
			type: String,
			required: false
		}
	},
	computed: {
		...mapGetters([
			'getElementStyleOptionsSchema'

		]),
		classTitle: {
			get () {
				return this.classItem.name
			},
			set (newVal) {
				this.$emit('input-classname', {
					name: newVal
				})
			}
		},
		getStyleOptionsModel: {
			get () {
				return this.classItem.style || {}
			},
			set (newValue) {
				this.$emit('input', {
					style: newValue
				})
			}
		}
	},
	data: () => {
		return {

		}
	},
	methods: {

	}
}

</script>
<style lang="scss">
.znpb-single-class-wrapper {
	display: flex;
	flex-direction: column;
	height: 100%;

	.znpb-input-wrapper--class-styling {
		padding: 0 20px;
		margin: 0;
	}
}
.znpb-input-wrapper--class-styling > .znpb-forms-input-content {
	position: relative;
	display: flex;

	& > .zion-input {
		flex: 6;
		margin-right: 10px;
	}
	& > div:last-child {
		margin-right: 0;
	}
}
</style>
