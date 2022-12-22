<template>
	<div class="znpb-class-selector">
		<Tooltip
			trigger="null"
			:show="dropdownState"
			placement="bottom-start"
			tooltip-class="znpb-class-selector__popper"
			:show-arrows="false"
		>
			<template #content>
				<div v-if="!allow_class_assignments">
					{{ __('Class assignments not allowed', 'zionbuilder') }}
				</div>
				<div
					v-else
					ref="dropDownWrapper"
					class="znpb-class-selector-body"
					tabindex="0"
					@keydown.down="onKeyDown"
					@keydown.up="onKeyUp"
					@keydown.enter="onKeyEnter"
					@keydown.esc.stop="dropdownState = false"
				>
					<div class="znpb-search-wrapper">
						<BaseInput
							ref="input"
							:modelValue="keyword"
							:filterable="true"
							:clearable="true"
							:placeholder="__('Enter Class Name', 'zionbuilder')"
							@update:modelValue="handleClassInput"
							@keydown.enter.stop="addNewCssClass"
						></BaseInput>
						<Button type="line" class="znpb-class-selector__add-class-button" @click="addNewCssClass">
							{{ __('Add Class', 'zionbuilder') }}
						</Button>
					</div>

					<template v-if="filteredClasses.length > 0">
						<CssSelector
							v-for="cssClassItem in filteredClasses"
							:key="cssClassItem.selector"
							:class-config="cssClassItem"
							:name="cssClassItem.name"
							:type="cssClassItem.type"
							:is-selected="cssClassItem.selector === activeClass"
							:show-delete="cssClassItem.deletable"
							@remove-class="removeClass"
							@click="selectClass(cssClassItem.selector), (dropdownState = false)"
							@copy-styles="onCopyStyles(cssClassItem)"
							@paste-styles="onPasteStyles(cssClassItem)"
						/>
					</template>
					<div v-if="!errorMessage && filteredClasses.length === 0" class="znpb-class-selector-noclass">
						{{
							__('No class found. Press "Add class" to create a new class and assign it to the element.', 'zionbuilder')
						}}
					</div>
					<div v-if="invalidClass" class="znpb-class-selector-validator">{{ errorMessage }}</div>
				</div>
			</template>

			<CssSelector
				v-bind="activeClassConfig"
				:class-config="activeClassConfig"
				:show-delete="false"
				:show-actions="false"
				class="znpb-class-selector-trigger"
				:show-changes-bullet="showRemoveExtraClasses"
				@click="dropdownState = !dropdownState"
				@remove-extra-classes="onRemoveExtraClasses"
			/>
		</Tooltip>
	</div>
</template>

<script>
import { __ } from '@wordpress/i18n';
import { computed } from 'vue';
import CssSelector from './CssSelector.vue';
import { useCSSClassesStore } from '/@/editor/store';

export default {
	name: 'ClassSelectorDropdown',
	components: {
		CssSelector,
	},
	props: {
		selector: {
			type: String,
			required: true,
		},
		modelValue: {
			type: Array,
		},
		title: {
			type: String,
		},
		activeClass: {
			type: String,
			required: true,
		},
		allow_class_assignments: {
			type: Boolean,
			required: false,
			default: true,
		},
		activeModelValue: {
			type: Object,
			required: false,
			default: {},
		},
	},
	setup(props, { emit }) {
		const cssClasses = useCSSClassesStore();
		const showRemoveExtraClasses = computed(() => {
			return props.modelValue && props.modelValue.length > 0;
		});

		function onCopyStyles(selectorConfig) {
			// If this is a
			if (selectorConfig.type === 'class') {
				// Get the class config
				const stylesConfig = cssClasses.getStylesConfig(selectorConfig.selector);
				cssClasses.copyClassStyles(stylesConfig);
			} else {
				// Get the config from id
				cssClasses.copyClassStyles(props.activeModelValue);
			}
		}

		function onPasteStyles(selectorConfig) {
			// If this is a
			if (selectorConfig.type === 'class') {
				// Get the class config
				cssClasses.pasteClassStyles(selectorConfig.selector);
			} else {
				// Get the config from id
				emit('paste-style-model');
			}
		}

		return {
			showRemoveExtraClasses,
			cssClasses,

			// Methods
			onCopyStyles,
			onPasteStyles,
		};
	},
	data() {
		return {
			dropdownState: false,
			errorMessage: null,
			keyword: '',
			invalidClass: false,
			focusClassIndex: 0,
		};
	},
	computed: {
		computedValue: {
			get() {
				return this.modelValue;
			},
			set(newValue) {
				this.$emit('update:modelValue', newValue);
			},
		},
		computedSelectorConfig() {
			return {
				type: 'selector',
				selector: this.selector,
				name: this.title,
				deletable: false,
			};
		},
		invalidClassReset() {
			return this.keyword.length > 0;
		},
		activeClassConfig() {
			if (this.activeClass !== this.selector) {
				const that = this;
				const className = this.cssClasses.CSSClasses.find(({ id }) => id === that.activeClass);
				return {
					type: 'class',
					name: className ? className.name : that.activeClass,
				};
			} else {
				return this.computedSelectorConfig;
			}
		},
		filteredClasses() {
			if (this.keyword.length === 0) {
				const extraClasses = [];
				this.computedValue.forEach(selector => {
					const className = this.cssClasses.CSSClasses.find(({ id }) => id === selector);

					if (className) {
						extraClasses.push({
							type: 'class',
							selector,
							name: className ? className.name : selector,
							deletable: true,
						});
					}
				});

				return [this.computedSelectorConfig, ...extraClasses];
			} else {
				return this.cssClasses.getClassesByFilter(this.keyword).map(selectorConfig => {
					return {
						type: 'class',
						selector: selectorConfig.id,
						name: selectorConfig.name,
					};
				});
			}
		},
	},
	watch: {
		dropdownState: function (newState, oldState) {
			if (newState) {
				document.addEventListener('click', this.closePanel);

				this.$nextTick(() => {
					if (this.$refs.input) {
						// Element not focused on nect tick alone
						setTimeout(() => {
							this.$refs.input.focus();
						}, 50);
					}
				});

				this.keyword = '';
			} else {
				document.removeEventListener('click', this.closePanel);
				this.errorMessage = null;
				this.focusClassIndex = 0;
			}
		},
	},
	beforeUnmount() {
		document.removeEventListener('click', this.closePanel);
	},
	methods: {
		onKeyDown(event) {
			let nextClass;

			if (this.filteredClasses.length !== 0) {
				this.$refs.input.blur();
				this.$refs.dropDownWrapper.focus();

				if (this.filteredClasses[this.focusClassIndex + 1]) {
					nextClass = this.filteredClasses[this.focusClassIndex + 1].selector;
					this.selectClass(nextClass);
					this.focusClassIndex += 1;
				}
			}
		},
		onKeyUp(event) {
			let previousClass;

			if (this.filteredClasses.length !== 0) {
				this.$refs.input.blur();
				this.$refs.dropDownWrapper.focus();

				if (this.filteredClasses[this.focusClassIndex - 1]) {
					previousClass = this.filteredClasses[this.focusClassIndex - 1].selector;
					this.selectClass(previousClass);
					this.focusClassIndex -= 1;
				}
			}
		},
		onKeyEnter(event) {
			this.dropdownState = false;
		},

		closePanel(event) {
			if (event.target === document) {
				this.dropdownState = false;
				return;
			}
			if (
				!this.$el.contains(event.target) &&
				event.target.tagName !== 'INPUT' &&
				!event.target.classList.contains('znpb-class-selector__add-class-button') &&
				!this.dragOutside
			) {
				this.dropdownState = false;
			}
		},

		onRemoveExtraClasses() {
			this.computedValue = [];
			this.selectClass(this.selector);
		},

		removeClass(selector) {
			const classIndex = this.computedValue.indexOf(selector);
			const clonedValue = [...this.computedValue];
			const previousClassIndex = classIndex - 1;
			const previousClassSelector = this.computedValue[previousClassIndex];

			clonedValue.splice(classIndex, 1);

			// Update the value
			this.computedValue = clonedValue;

			// Change active index if this was deleted
			if (selector === this.activeClass) {
				const newSelector = previousClassSelector || this.selector;
				const newSelectorIndex = clonedValue.indexOf(newSelector);

				this.selectClass(newSelector);

				// Add +1 as the main selector is not inside the selectors array
				this.focusClassIndex = newSelectorIndex !== -1 ? newSelectorIndex + 1 : 0;
			}

			// clear the keyword
			this.keyword = '';
			this.errorMessage = null;
		},
		handleClassInput(event) {
			this.keyword = event;

			// Add new class to store
			if (!/-?[_a-zA-Z]+[_a-zA-Z0-9-]*/i.test(this.keyword)) {
				this.errorMessage = 'Invalid class name, classes must not start with numbers and cannot contain spaces';
				this.invalidClass = true;
			} else {
				this.invalidClass = false;
				this.errorMessage = false;
			}

			if (!this.keyword.length) {
				this.errorMessage = false;
				this.invalidClass = false;
			}
		},
		addNewCssClass(event) {
			if (!this.invalidClass && this.keyword.length) {
				this.dropdownState = false;

				// check if the class already exists
				const existingClass = this.cssClasses.CSSClasses.find(classItem => {
					return classItem.name.toLowerCase() === this.keyword;
				});

				if (!existingClass) {
					this.cssClasses.addCSSClass({
						id: this.keyword,
						name: this.keyword,
					});
				}

				// Add css class to element options
				this.selectClass(this.keyword);

				// clear the keyword
				this.keyword = '';
			}
		},
		selectClass(selector) {
			// Check to see if we need to add the class to the element
			if (selector && selector !== this.selector && this.computedValue.indexOf(selector) === -1) {
				// Make a clone as computed properties with setters are not
				// triggered by array methods
				const clonedValue = [...this.computedValue];
				clonedValue.push(selector);
				this.computedValue = clonedValue;
			}

			this.$emit('update:activeClass', selector);
		},
	},
};
</script>

<style lang="scss">
.znpb-button.znpb-class-selector__add-class-button {
	padding: 12px;
	margin-left: 5px;
	white-space: nowrap;
}

.znpb-class-selector {
	flex: 6;
	margin-right: 10px;
	background: var(--zb-input-bg-color);
	border: 2px solid var(--zb-input-border-color);
	border-radius: 3px;
	.selected-class {
		.znpb-item {
			padding-top: 0;
		}
	}
	.znpb-css-class-selector__item-type {
		display: inline-block;
	}

	&-noclass {
		padding: 0 15px;
		line-height: 20px;
	}
	&-validator {
		color: #fff;
		text-align: center;
		background-color: var(--zb-error-color);
		border-radius: 3px;
		padding: 8px 12px;
	}
}

.znpb-class-selector-body {
	width: 288px;

	&:focus {
		outline: none;
	}

	.znpb-search-wrapper {
		display: flex;
		margin-bottom: 10px;
	}
}
</style>
