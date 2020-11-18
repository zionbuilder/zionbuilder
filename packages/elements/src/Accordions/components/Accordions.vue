<template>
	<SortableContent
		:element="data"
		:allow-elements-add="false"
		empty-placeholder-text="Edit Element options"
	>
		<template #start>
			<slot name="start" />
		</template>

		<template #end>
			<slot name="end" />
		</template>

	</SortableContent>
</template>

<script>
module.exports = {
	name: 'accordions',
	props: ['options', 'element', 'api']
}
</script>
