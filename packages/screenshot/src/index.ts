import domtoimage from "dom-to-image";

export default function screenshot() {
	window.addEventListener('load', onDocumentLoad)

	function onDocumentLoad(event) {
		getScreenshot()
	}

	function getScreenshot() {
		domtoimage
			.toPng(document.body, {
				style: {
					width: "100%",
					margin: 0,
				}
			})
			.then((dataUrl) => {
				console.log({
					dataUrl
				});
			})
			.catch((error) => {
				console.error(error);
			})
	}
}

screenshot()
