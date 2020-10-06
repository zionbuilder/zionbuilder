<template>
	<div>
		<slot name="start" />
		<RenderValue option="content" />
		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'zion_text',
	props: ['options', 'data', 'api']
}
</script>
