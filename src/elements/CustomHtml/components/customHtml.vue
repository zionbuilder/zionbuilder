<template>
	<div>
		<slot name="start" />

		<div v-html="options.content"></div>

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'custom_html',
	props: ['options', 'data', 'api']
}
</script>
