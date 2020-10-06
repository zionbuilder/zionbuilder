<template>
	<div>
		<slot name="start" />

		{{getCssID}}

		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'anchor_point',
	props: ['options', 'data', 'api'],
	computed: {
		getCssID () {
			return (this.options._advanced_options || {})._element_id || this.data.uid
		}
	}
}
</script>
