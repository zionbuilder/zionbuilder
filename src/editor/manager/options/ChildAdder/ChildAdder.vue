<template>
	<div class="znpb-options-childs__wrapper">
		<Sortable
			class="znpb-options-childs__items-wrapper"
			v-model="elementContent"
		>

			<SingleChild
				v-for="element in elementContent"
				:key="element"
				:element-uid="element"
				:item-option-name="item_name"
				@delete="onElementDelete"
				@clone="onCloneElement"
			/>

		</Sortable>
		<BaseButton
			class="znpb-option-repeater__add-button"
			type="line"
			@click.native="addChild"
		>
			ADD
		</BaseButton>
	</div>
</template>

<script>
import { mapActions } from 'vuex'
import { Sortable } from '@/common/vue-beautifull-dnd'
import SingleChild from './SingleChild'

export default {
	name: 'Childs',
	components: {
		Sortable,
		SingleChild
	},
	inject: {
		elementInfo: {
			default: null
		}
	},
	props: {
		value: {
			default () {
				return {}
			}
		},
		child_type: {
			type: String,
			required: true
		},
		item_name: {
			type: String,
			required: true
		}
	},
	data () {
		return {}
	},
	computed: {
		elementUid () {
			return this.elementInfo.data.data.uid
		},
		elementContent: {
			get () {
				return this.elementInfo.data.data.content
			},
			set (newValue) {
				this.saveElementsOrder({
					newOrder: newValue,
					content: this.elementInfo.data.data.content
				})
			}
		}
	},
	methods: {
		...mapActions([
			'addElement',
			'saveElementsOrder',
			'deleteElement',
			'copyElement'
		]),
		addChild () {
			let elementData = {
				element_type: this.child_type
			}

			this.addElement({
				parentUid: this.elementUid,
				index: this.elementContent.length,
				data: elementData
			})
		},
		onElementDelete (elementUid) {
			this.deleteElement({
				elementUid,
				parentUid: this.elementUid
			})
		},
		onCloneElement (elementUid) {
			this.copyElement({
				elementUid,
				insertParent: this.elementUid
			})
		}
	}
}
</script>

<style lang="scss">
.znpb-options-childs {
	&__element {
		padding: 12px 15px;
		margin-bottom: 5px;
	}
}
</style>
