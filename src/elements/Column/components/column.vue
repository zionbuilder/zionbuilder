<template>
	<SortableContent
		class="zb-column"
		:content="data.content"
		:data="data"
		:tag="htmlTag"
	>
		<slot name="start" slot="start" />
		<slot name="end" slot="start" />
	</SortableContent>
</template>

<script>
export default {
	name: 'zion_column',
	props: ['options', 'data', 'api'],
	computed: {
		htmlTag () {
			return this.options.tag || 'div'
		}
	}
}
</script>
