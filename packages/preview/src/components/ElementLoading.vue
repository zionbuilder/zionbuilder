<template>
	<div class="znpb-preview__element-loading">
		<img :src="loadingUrl" />
	</div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
	name: 'ElementLoading',
	data () {
		return {
			loading: false
		}
	},
	computed: {
		...mapGetters([
			'getAssetsUrl'
		]),
		loadingUrl () {
			return this.getAssetsUrl + '/img/zion-loader-slim--small.gif'
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
