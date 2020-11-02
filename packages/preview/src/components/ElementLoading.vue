<template>
	<div class="znpb-preview__element-loading">
		<img :src="loadingUrl" />
	</div>
</template>

<script>

export default {
	name: 'ElementLoading',
	setup () {
		const { urls } = useEditorData()

		return {
			urls
		}
	},
	computed: {
		loadingUrl () {
			return this.urls.assets_url + '/img/zion-loader-slim--small.gif'
		}
	}
}
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
