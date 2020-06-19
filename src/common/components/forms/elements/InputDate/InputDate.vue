<template>
	<div class="znpb-input-date">
		<date-pick v-model="valueModel">

			<template v-slot:default="{toggle, inputValue}">
				<BaseInput
					v-model="valueModel"
					class="znpb-input-number__input"
					v-bind="$attrs"
					@keydown.native="toggle"
					@mouseup.native="toggle"
				>
				</BaseInput>
			</template>
		</date-pick>
	</div>
</template>

<script>
import DatePick from 'vue-date-pick'
import BaseInput from '../input/BaseInput'
import 'vue-date-pick/dist/vueDatePick.css'
/**
 *   model - string
 */
export default {
	name: 'InputDate',
	components: {
		DatePick,
		BaseInput
	},
	props: {
		/**
		 * Value for input
		 */
		value: {
			type: String,
			required: true
		}
	},
	data () {
		return {}
	},
	computed: {
		valueModel: {
			get () {
				return this.value
			},
			set (newValue) {
				/**
					 *It emits the new value
					 */
				this.$emit('input', newValue)
			}
		}
	},
	methods: {
	}
}
</script>
<style lang="scss">
</style>
