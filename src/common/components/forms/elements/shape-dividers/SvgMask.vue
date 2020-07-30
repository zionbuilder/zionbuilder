<template>
	<div
		class="znpb-shape-divider-icon znpb-mask"
		v-html="getSvgIcon"
		:class="[position==='top' ?'-pos--top': '-pos--bottom']"
	>

	</div>
</template>
<script>
import axios from 'axios'
let restConfig = window.ZnPbRestConfig

export default {
	name: 'SvgMask',
	props: {
		/**
		 * Value for input
		 */
		shapePath: {
			type: String,
			required: false
		},
		position: {
			type: String,
			required: false
		}
	},
	data () {
		return {
			svgData: ''
		}
	},
	computed: {
		getSvgIcon () {
			return `${this.svgData}`
		}
	},
	watch: {
		shapePath (newvalue) {
			this.getFile(newvalue)
		}

	},
	mounted () {
		if (this.shapePath !== undefined && this.shapePath.length) {
			this.getFile(this.shapePath)
		}
	},
	methods: {
		getFile (shapePath) {
			axios({
				url: shapePath,
				method: 'GET',
				headers: {
					'X-WP-Nonce': restConfig.nonce,
					'Accept': 'application/json',
					'Content-Type': 'application/json'
				}
			}).then((response) => {
				this.svgData = response.data
			}).catch(error => {
				console.error(error)
			})
		}
	}
}
</script>
