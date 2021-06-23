<template>
	<div class="znpb-system-list-wrapper">
		<h2 class="znpb-system-title">{{$translate('copy_paste')}}</h2>
		<h5 class="znpb-system-subtitle">{{$translate('copy_paste_description')}}</h5>

		<BaseInput
			:modelValue="getCategoryData"
			type="textarea"
			class="znpb-system-textarea"
			readonly
			spellcheck="false"
			autocomplete="false"
		/>
	</div>
</template>
<script>
import { computed } from 'vue'
export default {
	name: 'CopyPasteServer',
	props: {
		categoryData: {
			type: Array,
			required: true
		}
	},
	setup (props) {
		const getCategoryData = computed(() => {
			let result = []

			props.categoryData.forEach((category) => {
				result.push(`==${category.category_name}==\n`)

				Object.keys(category.values).forEach(function (key) {
					result.push(`\t${category.values[key].name}`)
					// statement for plugins which don't have value
					if (category.values[key].value !== undefined) {
						result.push(`: ${category.values[key].value}`)
					}
					result.push('\n')
				})
			})

			return result.join('')
		})

		return {
			getCategoryData
		}
	}

}
</script>
<style lang="scss" scoped>
.znpb-system-title {
	font-size: 24px;
	font-weight: 500;
}
.znpb-system-subtitle {
	color: var(--zb-surface-text-color);
	font-size: 13px;
	font-weight: 400;
	text-transform: capitalize;
}

.znpb-system-textarea {
	min-width: 480px;
	min-height: 320px;
	margin-bottom: 30px;
	line-height: 27px;
	text-transform: capitalize;
	white-space: pre-wrap;
	border-radius: 3px;

	@media (max-width: 767px) {
		min-width: auto;
		max-width: 100%;
	}
}
</style>
