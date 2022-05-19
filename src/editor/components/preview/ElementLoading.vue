<template>
	<div class="znpb-preview__element-loading">
		<img :src="loadingUrl" />
	</div>
</template>

<script>
import { inject } from 'vue'
export default {
	name: 'ElementLoading',
	setup () {
		const urls = inject('editor_urls')

		return {
			urls
		}
	},
	computed: {
		loadingUrl () {
			return this.urls.loader
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
