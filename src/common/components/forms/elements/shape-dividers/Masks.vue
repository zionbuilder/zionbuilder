<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
let restConfig = window.ZnPbRestConfig

export default {
	data () {
		return {
			svgData: ''
		}
	},
	computed: {

		...mapGetters([
			'getAssetsUrl'
		]),

		getSvgIcon () {
			return `${this.svgData}`
		}
	},
	watch: {
		shapeType (newvalue) {
			this.getFile(newvalue)
		}
	},
	mounted () {
		if (this.shapeType.length) {
			this.getFile(this.shapeType)
		}
	},
	methods: {
		getFile (shapeType) {
			axios({
				url: `${this.getAssetsUrl}/masks/${shapeType}.svg`,
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
