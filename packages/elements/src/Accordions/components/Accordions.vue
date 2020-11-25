<template>
	<SortableContent
		:element="element"
		:allow-elements-add="false"
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
	props: ['options', 'element', 'api'],
	setup(props) {
		// Check to see if we need to add some items
		if (props.element.content.length === 0 && props.options.items) {
			props.element.addChildren(props.options.items)
		}

		return {}
	}
}
</script>
