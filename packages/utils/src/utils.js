/**
 * Generate a unique ID based on current date
 */
export function generateUID() {
	return (function (index, lastDateInSeconds) {
		// Get shorter indexes by setting the start date to 2018 year
		const startDate = new Date('2019');
		return function () {
			const d = new Date();
			const n = d - startDate;

			// Set initial date number
			if (lastDateInSeconds === false) {
				lastDateInSeconds = n;
			}

			// Reset the index if the date has changed
			if (lastDateInSeconds !== n) {
				index = 0;
			}

			// Set the last date so we can compare it on multiple iterations
			lastDateInSeconds = n;

			// Increase the unique index
			index += 1;

			// Return the unique index
			return 'uid' + n + index;
		};
	})(0, false);
}

export function youtubeUrlParser(url) {
	let regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
	let match = url.match(regExp);
	return match && match[7].length === 11 ? match[7] : false;
}
