<template>
	<div>
		<slot name="start" />
		<RenderValue option="content" />
		<slot name="end" />
	</div>
</template>

<script>
export default {
	name: 'ZionText',
	props: ['options', 'element', 'api'],
};
</script>
