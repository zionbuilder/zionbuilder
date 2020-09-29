<template>
	<div
		ref="currentCateg"
		:class="{'znpb-active': isExpanded}"
		class="znpb-wrapper-category "
	>
		<h6
			v-if="catindex!=undefined"
			class="znpb-category-title"
			@click="$emit('activate-category', catindex)"
		>{{ category.name }}</h6>

		<transition
			name="expand"
			@enter="enter"
			@after-enter="afterEnter"
			@leave="leave"
		>
			<div
				v-if="isExpanded"
				class="znpb-ul-wrapper znpb-fancy-scrollbar"
				@scroll.passive="initScroll"
				ref="scrollElem"
			>
				<ElementList
					:elements="elements"
					:tag="tag"
				/>
			</div>
		</transition>
		<ElementList
			v-if="catindex==undefined"
			:elements="elements"
			:tag="tag"
		/>

	</div>
</template>
<script>

import rafSchedule from 'raf-schd'
import { mapGetters } from 'vuex'
import ElementList from './ElementList.vue'

export default {
	name: 'CategoriesElements',
	inheritAttrs: false,
	components: {
		ElementList
	},
	props: {
		category: {
			type: Object,
			required: true
		},
		isExpanded: {
			type: Boolean,
			default: false
		},
		catindex: {
			type: Number,
			required: false
		},
		tag: {
			type: String,
			required: false,
			default: 'Sortable'
		},
		elementsNumber: {
			type: Number,
			required: false
		}
	},
	data () {
		return {
			localIsExpanded: this.isExpanded
		}
	},
	computed: {
		...mapGetters([
			'getElementsByCategory'
		]),
		elements () {
			return this.getElementsByCategory(this.category.id)
		}
	},
	methods: {
		initScroll () {
			const schedule = rafSchedule(this.startScroll())

			window.addEventListener('scroll', function () {
				schedule(window.scrollY)
			})
		},
		startScroll () {
			window.scroll({
				behavior: 'smooth'
			})
			let elem = this.$refs.scrollElem
			if (elem !== undefined) {
				// scroll up
				if ((elem.scrollTop === 0) && (this.catindex !== 0)) {
					this.$emit('activate-category', this.catindex - 1)
				}

				// scroll down
				const height = getComputedStyle(elem).height
				let scrollHeight = elem.scrollHeight
				if ((parseInt(height, 10) + elem.scrollTop) === scrollHeight) {
					if ((this.elementsNumber - 1) === this.catindex) {
					} else this.$emit('activate-category', this.catindex + 1)
				}
			}
		},
		enter (element) {
			const width = getComputedStyle(element).width

			element.style.width = width
			element.style.position = 'absolute'
			element.style.visibility = 'hidden'
			element.style.height = 'auto'

			const height = getComputedStyle(element).height

			element.style.width = null
			element.style.position = null
			element.style.visibility = null
			element.style.height = 0

			setTimeout(() => {
				element.style.height = height
			})
		},
		afterEnter (element) {
			element.style.height = null
		},
		leave (element) {
			const height = getComputedStyle(element).height

			element.style.height = height
			setTimeout(() => {
				element.style.height = 0
			})
		}
	},
	beforeUnmount () {
		// Detach the listener when the component is gone

		window.removeEventListener('scroll', this.startScroll())
	}
}
</script>
<style lang="scss">
/* vars */

.znpb-ul-wrapper {
	display: flex;
	flex-direction: column;
	width: 100%;
	padding: 0 15px;
	margin-bottom: 40px;
}
.znpb-wrapper-category {
	display: flex;
	flex-direction: column;
	max-height: 100%;
	transition: all .2s;

	.znpb-category-title {
		padding: 15px;
		color: darken($surface-variant, 15%);
		font-size: 11px;
		font-weight: bold;
		text-transform: uppercase;
		cursor: pointer;
	}
	&.znpb-active {
		.znpb-category-title {
			position: relative;
			width: 100%;
			color: darken($surface-headings-color, 20%);
		}
	}
}

.expand-enter-active, .expand-leave-active {
	overflow: hidden;
	transition: height .5s ease-in-out;
	will-change: height;
}
</style>
