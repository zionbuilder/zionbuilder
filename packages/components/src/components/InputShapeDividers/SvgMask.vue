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

export default {
	name: 'SvgMask',
	props: {
		/**
		 * Value for input
		 */
		shapePath: {
			type: [String, Object],
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
		flip: {
			type: Boolean,
			required: false
		}
	},
	setup (props) {
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
			let url;
			if (shapePath.indexOf('.svg') !== -1) {
				url = shapePath
			} else {
				const shapeConfig = this.masks[shapePath]
				url = shapeConfig.url
			}

			fetch(url)
				.then(response => response.text())
				.then(svgFile => {
					this.svgData = svgFile
				})
				.catch(error => {
					console.error(error)
				})
		}
	}
}
</script>
