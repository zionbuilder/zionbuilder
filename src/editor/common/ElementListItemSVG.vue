<script lang="ts">
import { h, ref } from 'vue';

const cache = {};

export default {
	name: 'ElementListItemSVG',
	props: ['svg'],
	setup(props) {
		const iconMarkup = ref('');

		if (cache[props.svg]) {
			iconMarkup.value = cache[props.svg];
		} else {
			fetch(props.svg)
				.then(response => response.text())
				.then(data => {
					iconMarkup.value = data;
					cache[props.svg] = data;
				});
		}

		return () =>
			h('span', {
				class: 'znpb-editor-icon-wrapper znpb-editor-icon-wrapper--isSVG',
				innerHTML: iconMarkup.value,
			});
	},
};
</script>

<style>
.znpb-editor-icon-wrapper--isSVG svg {
	display: block;
	width: 1em;
	height: 1em;
	margin: auto;
}
</style>
