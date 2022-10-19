<template>
	<div class="znpb-preview__element-loading">
		<img :src="imageSrc" />
	</div>
</template>

<script lang="ts" setup>
const imageSrc = window.ZnPbInitialData.urls.loader;
</script>

<style lang="scss">
.znpb-preview__element-loading {
	text-align: center;

	img {
		width: 50px;
		height: auto;
	}
}
</style>
