<script>
import { h } from 'vue';

export default {
	name: 'ElementStyles',
	props: {
		styles: {
			type: String,
		},
	},
	setup(props) {
		return () => {
			if (props.styles) {
				return h('style', props.styles);
			}
			return null;
		};
	},
};
</script>
