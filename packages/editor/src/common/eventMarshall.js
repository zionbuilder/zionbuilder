export default {
	activeTooltip: null,

	addActiveTooltip (tooltipVm) {
		this.activeTooltip = tooltipVm
	},
	get getActiveTooltip () {
		return this.activeTooltip
	},
	reset () {
		this.activeTooltip = null
	}
}
