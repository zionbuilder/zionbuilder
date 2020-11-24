<template>
	<div
		class="znpb-option__html"
		v-html="content"
	>
	</div>
</template>

<script>
export default {
	name: 'HTML',
	props: {
		content: {
			type: String,
			required: false
		}
	}
}
</script>
<style lang="scss">
</style>
