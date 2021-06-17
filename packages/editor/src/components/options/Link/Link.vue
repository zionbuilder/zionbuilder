<template>
	<div class="znpb-link-wrapper">
		<InputWrapper layout="full">
			<component
				:is="linkURLComponent"
				v-model="link"
				:placeholder="$translate('add_an_url')"
				:title="$translate('link_url')"
			>

				<template v-slot:prepend>
					<Icon icon="link"></Icon>
				</template>

				<template v-slot:append>
					<Tooltip
						trigger="click"
						:close-on-outside-click="true"
						append-to="body"
						tooltip-class="znpb-link-optionsTooltip"
						placement="bottom-end"
					>
						<template #content>
							<div class="znpb-link-options">
								<div class="znpb-link-options-title">{{$translate('link_attributes')}}</div>
								<div class="znpb-link-optionsAttributes">
									<LinkAttributeForm
										v-for="(attribute, index) in linkAttributes"
										:key="index"
										:attribute-config="attribute"
										:can-delete="canDeleteAttributes"
										@update-attribute="onAttributeUpdate(index, $event)"
										@delete="deleteAttribute(index)"
									/>

									<div
										class="znpb-link-optionsAttributesAdd"
										@click="addLinkAttribute"
									>
										<Icon icon="plus" /> <span>{{$translate('add_custom_link_attribute')}}</span>
									</div>
								</div>
							</div>
						</template>

						<Icon icon="tags-attributes"></Icon>
					</Tooltip>

					<!-- Injection point -->
					<Injection location="options/link/append" />
				</template>

			</component>
		</InputWrapper>
		<InputWrapper
			layout="inline"
			:schema="schema"
		>
			<InputSelect
				:options="targetOptions"
				v-model="target"
				:title="$translate('link_target')"
			></InputSelect>
		</InputWrapper>
		<InputWrapper
			layout="inline"
			:schema="schema"
		>
			<BaseInput
				v-model="title"
				:clearable="false"
				:title="$translate('link_title')"
				:placeholder="$translate('set_a_title')"
			/>
		</InputWrapper>
	</div>
</template>

<script>
import { get } from 'lodash-es'
import { computed } from 'vue'
import { Injection } from "@zb/components"
import { applyFilters } from '@zb/hooks'
import { Tooltip } from '@zionbuilder/tooltip'
import LinkAttributeForm from './LinkAttributeForm.vue'

export default {
	name: 'Link',
	props: {
		modelValue: {
			default () {
				return {}
			}
		}
	},
	components: {
		Injection,
		Tooltip,
		LinkAttributeForm
	},
	setup (props, { emit }) {
		const linkURLComponent = computed(() => {
			return applyFilters('zionbuilder/options/link/url_component', 'BaseInput', props.modelValue)
		})

		const linkAttributes = computed({
			get () {
				let attributes = get(props.modelValue, 'attributes')
				if (Array.isArray(attributes) && attributes.length > 0) {
					return attributes
				} else {
					return [
						{
							key: '',
							value: ''
						}
					]
				}
			},
			set (newValue) {
				emit('update:modelValue', {
					...props.modelValue,
					attributes: newValue
				})
			}
		})

		const canDeleteAttributes = computed(() => linkAttributes.value.length > 1)

		function addLinkAttribute () {
			linkAttributes.value = [
				...linkAttributes.value,
				{
					key: '',
					value: ''
				}
			]
		}

		function deleteAttribute (index) {
			const clone = [
				...linkAttributes.value
			]

			clone.splice(index, 1)
			linkAttributes.value = clone
		}

		function onAttributeUpdate (index, attribute) {
			const clone = [
				...linkAttributes.value
			]

			clone.splice(index, 1, attribute)

			linkAttributes.value = clone
		}

		return {
			linkURLComponent,
			addLinkAttribute,
			linkAttributes,
			deleteAttribute,
			onAttributeUpdate,
			canDeleteAttributes
		}
	},
	data () {
		return {
			targetOptions: [
				{
					id: '_blank',
					name: this.$translate('link_new_window')
				},
				{
					id: '_self',
					name: this.$translate('link_blank')
				}
			]
		}
	},
	computed: {
		link: {
			get () {
				return this.modelValue && this.modelValue['link'] ? this.modelValue['link'] : ''
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					'link': newValue
				})
			}
		},
		target: {
			get () {
				return this.modelValue && this.modelValue['target'] ? this.modelValue['target'] : '_self'
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					'target': newValue
				})
			}
		},
		title: {
			get () {
				return this.modelValue && this.modelValue['title'] ? this.modelValue['title'] : ''
			},
			set (newValue) {
				this.$emit('update:modelValue', {
					...this.modelValue,
					'title': newValue
				})
			}
		},
		schema () {
			return {
				width: 50
			}
		}
	}
}
</script>

<style lang="scss">
.znpb-link-wrapper {
	display: flex;
	flex-wrap: wrap;
	margin: 0 -5px;

	& > .znpb-input-wrapper {
		&:first-child {
			padding-bottom: 10px;
		}

		& > .znpb-forms-input-content {
			display: flex;

			label {
				margin-right: 10px;

				&:last-child {
					margin-right: 0;
				}
			}
		}

		& .zion-input__prepend {
			background: $surface-variant;
		}

		& .zion-input__append {
			.zion-tags-attributes {
				margin-right: 5px;
				cursor: pointer;

				&:hover {
					color: #959595;
				}
			}
		}
	}

	& > .zion-input {
		margin-bottom: 15px;
	}
}

.znpb-link-optionsTooltip {
	width: 320px;
}

.znpb-link-optionsAttributesAdd {
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 10px 5px 5px;
	font-weight: 500;
	line-height: 1;
	cursor: pointer;

	.znpb-editor-icon-wrapper {
		margin-right: 3px;
	}
}

.znpb-link-options-title {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 5px;
	margin-bottom: 10px;
	color: #5f5f5f;
	font-family: "Roboto", sans-serif;
	font-size: 13px;
	font-weight: 500;
	line-height: 14px;
}
</style>
