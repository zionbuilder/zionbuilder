<script>
export default {
	props: {
		schema: {
			type: Object,
			required: true
		}
	},

	provide () {
		return {
			inputWrapper: this
		}
	},
	render: function (h) {
		return this.$slots.default
	}
}
</script>
