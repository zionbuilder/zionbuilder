<template>
	<div
		class="znpb-shape-divider-icon zb-mask"
		v-html="getSvgIcon"
		:class="[
			position==='top' ?'zb-mask-pos--top': 'zb-mask-pos--bottom',
			flip ? 'zb-mask-pos--flip': ''
		]"
		:style="{'color':color}"
	>

	</div>
</template>
<script>
import { inject } from 'vue'
import axios from 'axios'

export default {
	name: 'SvgMask',
	props: {
		/**
		 * Value for input
		 */
		shapePath: {
			type: String,
			required: true
		},
		position: {
			type: String,
			required: false
		},
		color: {
			type: String,
			required: false
		},
		height: {
			type: String,
			required: false
		},
		flip: {
			type: Boolean,
			required: false
		}
	},
	setup () {
		const masks = inject('masks')

		return {
			masks
		}
	},
	data () {
		return {
			svgData: ''
		}
	},
	computed: {
		getSvgIcon () {
			return this.svgData
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
			const url = shapePath.indexOf('.svg') === -1 ? this.masks[shapePath] : shapePath
			axios({
				url,
				method: 'GET'
			}).then((response) => {
				this.svgData = response.data
			}).catch(error => {
				console.error(error)
			})
		}
	}
}
</script>
