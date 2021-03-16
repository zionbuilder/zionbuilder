<script>
import { h, ref } from 'vue'

export default {
	name: 'ElementListItemSVG',
	props: ['svg'],
	setup (props) {
		const iconMarkup = ref(false)

		fetch(props.svg)
			.then(response => response.text())
			.then(data => iconMarkup.value = data)

		return () => h('span', {
			class: 'znpb-editor-icon-wrapper znpb-editor-icon-wrapper--isSVG',
			innerHTML: iconMarkup.value
		})
	}
}
</script>

<style>
.znpb-editor-icon-wrapper--isSVG svg {
	display: block;
	width: 1em;
	height: 1em;
	margin: auto;
	font-size: 47px;
}
</style>