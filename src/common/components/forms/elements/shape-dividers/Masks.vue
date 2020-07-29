<script>
import axios from 'axios'
let restConfig = window.ZnPbRestConfig

export default {
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
		shapeType (newvalue) {
			this.shapeType = newvalue
		}
	},
	mounted () {
		if (this.shapePath.length) {
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
