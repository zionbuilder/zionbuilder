<template>
	<SortableContent
		:content="data.content"
		:data="data"
		:allow-elements-add="false"
		empty-placeholder-text="Edit Element options"
	>
		<slot name="start" slot="start" />
		<slot name="end" slot="start" />
	</SortableContent>
</template>

<script>
module.exports = {
	name: 'accordions',
	props: ['options', 'data', 'api']
}
</script>
