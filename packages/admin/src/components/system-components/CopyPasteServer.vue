<template>
	<div class="znpb-system-list-wrapper">
		<h2 class="znpb-system-title">{{$translate('copy_paste')}}</h2>
		<h5 class="znpb-system-subtitle">{{$translate('copy_paste_description')}}</h5>

		<BaseInput
			:modelValue="getCategoryData"
			type="textarea"
			class="znpb-system-textarea"
			readonly spellcheck="false"
			autocomplete="false"
		/>
	</div>
</template>

<script>
export default {
	name: 'CopyPasteServer',
	props: {
		CategoryData: {
			type: Array,
			required: true
		}
	},
	data () {
		return {}
	},
	computed: {
		getCategoryData () {
			let result = []

			this.CategoryData.forEach((category) => {
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
	color: $font-color;
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
}
</style>
