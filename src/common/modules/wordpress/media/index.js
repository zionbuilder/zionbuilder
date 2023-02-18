window.addEventListener('load', () => {
	const wp = window.wp;
	const Library = wp.media.controller.Library;
	const _ = window._;
	const Select = window.wp.media.view.MediaFrame.Select;

	/**
	 * Media Controller
	 *
	 * Mainly sets the selected image the first.
	 *
	 */
	const MediaController = Library.extend(
		/** @lends wp.media.controller.FeaturedImage.prototype */
		{
			defaults: _.defaults(
				{
					id: 'zion-media',
					filterable: 'uploaded',
					priority: 60,
					syncSelection: true,
				},
				Library.prototype.defaults,
			),

			/**
			 * @since 1.0.0
			 */
			initialize: function () {
				// eslint-disable-next-line prefer-rest-params
				Library.prototype.initialize.apply(this, arguments);

				const library = this.get('library');
				const comparator = library.comparator;

				// Overload the library's comparator to push items that are not in
				// the mirrored query to the front of the aggregate collection.
				library.comparator = function (a, b) {
					const aInQuery = !!this.mirroring.get(a.cid);
					const bInQuery = !!this.mirroring.get(b.cid);
					if (!aInQuery && bInQuery) {
						return -1;
					} else if (aInQuery && !bInQuery) {
						return 1;
					} else {
						// eslint-disable-next-line prefer-rest-params
						return comparator.apply(this, arguments);
					}
				};

				// Add all items in the selection to the library, so any featured
				// images that are not initially loaded still appear.
				library.observe(this.get('selection'));
			},
		},
	);

	const ZionBuilderFrame = Select.extend(
		/** @lends wp.media.view.MediaFrame.Post.prototype */ {
			initialize: function () {
				// eslint-disable-next-line prefer-rest-params
				Select.prototype.initialize.apply(this, arguments);
			},

			createStates: function () {
				const options = this.options;
				this.states.add(
					new MediaController({
						library: wp.media.query(options.library),
						multiple: options.multiple,
						title: options.title,
					}),
				);
			},
		},
	);

	window.wp.media.view.MediaFrame.ZionBuilderFrame = ZionBuilderFrame;
});
