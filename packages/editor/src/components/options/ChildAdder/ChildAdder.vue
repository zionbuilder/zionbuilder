<template>
	<div class="znpb-options-childs__wrapper">
		<Sortable
			class="znpb-options-childs__items-wrapper"
			v-model="elementContent"
		>

			<SingleChild
				v-for="element in elementContent"
				:key="element.uid"
				:element="element"
				:item-option-name="item_name"
				@delete="onElementDelete"
				@clone="onCloneElement"
			/>

		</Sortable>
		<Button
			class="znpb-option-repeater__add-button"
			type="line"
			@click="addChild"
		>
			ADD
		</Button>
	</div>
</template>

<script>
import { computed } from 'vue'
import { useElementProvide, useElements } from '@data'
import SingleChild from './SingleChild.vue'

export default {
	name: 'Childs',
	components: {
		SingleChild
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
	setup(props) {
		const { injectElement } = useElementProvide()
		const { getElement } = useElements()
		const element = injectElement()

		const elementContent = computed({
			get () {
				return element.value.content.map(element => getElement(element))
			},
			set (newValue) {
				element.value.content = newValue.map(element => element.uid)
			}
		})

		const addChild = () => {
			element.value.addChild({
				element_type: props.child_type
			})
		}

		const onElementDelete = (elementUid) => {
			const element = getElement(elementUid)
			element.delete()
		}

		const onCloneElement = (elementUid) => {
			const element = getElement(elementUid)
			element.duplicate()
		}

		return {
			element,
			elementContent,
			addChild,
			onElementDelete,
			onCloneElement
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
