import MediaController from './MediaController'

const wp = window.wp
const Select = window.wp.media.view.MediaFrame.Select

const ZionBuilderFrame = Select.extend(/** @lends wp.media.view.MediaFrame.Post.prototype */{
	initialize: function () {
		Select.prototype.initialize.apply(this, arguments)
	},

	createStates: function () {
		const options = this.options
		this.states.add(new MediaController({
			library: wp.media.query(options.library),
			multiple: options.multiple,
			title: options.title
		}))
	}
})

window.wp.media.view.MediaFrame.ZionBuilderFrame = ZionBuilderFrame
