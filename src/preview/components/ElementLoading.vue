<template>
	<div class="znpb-preview__element-loading">
		<img :src="window.ZnPbInitialData.urls.loader" />
	</div>
</template>

<style lang="scss">
.znpb-preview__element-loading {
	text-align: center;

	img {
		width: 50px;
		height: auto;
	}
}
</style>
