<template>
	<div class="znpb-options-form-wrapper">
		<template v-for="(optionConfig, optionId) in optionsSchema" :key="optionId">
			<div v-if="optionConfig.breadcrumbs" class="znpb-options-breadcrumbs-path znpb-options-breadcrumbs-path--search">
				<div v-for="(breadcrumb, i) in optionConfig.breadcrumbs" :key="i" class="znpb-options-breadcrumbs-path">
					<span v-html="optionConfig.breadcrumbs[i]"></span>
					<Icon v-if="i <= optionConfig.breadcrumbs.length" icon="select" class="znpb-options-breadcrumbs-path-icon" />
				</div>
				<span v-html="optionConfig.title"></span>
			</div>

			<OptionWrapper
				:schema="optionConfig"
				:option-id="optionId"
				:modelValue="optionConfig.is_layout ? modelValue : modelValue[optionId]"
				:compile-placeholder="compilePlaceholder"
				@update:modelValue="setValue(...$event)"
				@change="onOptionChange"
			/>
		</template>
	</div>
</template>

<script>
import { provide, inject, computed } from 'vue';
import { useResponsiveDevices, usePseudoSelectors } from '../../composables';
import { unset, set, get, cloneDeep } from 'lodash-es';
import { useDataSetsStore } from '../../store/useDataSetsStore';

// Components
import OptionWrapper from './OptionWrapper.vue';

export default {
	name: 'OptionsForm',
	components: {
		OptionWrapper,
	},
	provide() {
		return {
			showChanges: this.showChanges,
			optionsForm: this,
		};
	},
	props: {
		modelValue: {},
		schema: {
			type: Object,
			required: true,
		},
		showChanges: {
			required: false,
			default: true,
		},
		replacements: {
			type: Array,
			required: false,
			default: () => [],
		},
	},
	setup(props, { emit }) {
		// Provide the top model value so we can check for sync options values
		let topModelValue = inject('OptionsFormTopModelValue', null);

		if (null === topModelValue) {
			topModelValue = computed(() => props.modelValue);
			provide('OptionsFormTopModelValue', () => topModelValue);
		}

		const { activeResponsiveDeviceInfo } = useResponsiveDevices();
		const { activePseudoSelector } = usePseudoSelectors();

		/**
		 * Will update the top model
		 *
		 * This will mutate state
		 */
		function updateTopModelValueByPath(path, newValue) {
			set(topModelValue.value, path, newValue);
		}

		/**
		 * Will delete the top model value by specifying a path
		 *
		 * This will mutate state
		 */
		function deleteTopModelValueByPath(path) {
			unset(topModelValue.value, path);
			deleteNested(path, topModelValue.value);
		}

		/**
		 * Returns a value for the top level model value by specifying a path
		 */
		function getTopModelValueByPath(path, defaultValue = undefined) {
			return get(topModelValue.value, path, defaultValue);
		}

		const getValueByPath = (path, defaultValue = null) => {
			return get(props.modelValue, path, defaultValue);
		};

		/**
		 * Will update a value by path
		 */
		const updateValueByPath = (path, newValue) => {
			const clonedValue = cloneDeep(props.modelValue);
			set(clonedValue, path, newValue);
			emit('update:modelValue', clonedValue);
		};

		function deleteNestedEmptyObjects(paths, object) {
			paths.forEach(path => {
				const remainingPaths = paths.slice(1, paths.length);

				if (typeof object[path] === 'object') {
					object[path] = deleteNestedEmptyObjects(remainingPaths, object[path]);

					if (Object.keys(object[path]).length === 0) {
						delete object[path];
					}
				}
			});

			return object;
		}

		function deleteNested(path, model) {
			const paths = path.split('.');
			paths.pop();
			deleteNestedEmptyObjects(paths, model);
		}

		const deleteValueByPath = path => {
			const clonedValue = cloneDeep(props.modelValue);
			unset(clonedValue, path);
			deleteNested(path, clonedValue);

			if (Object.keys(clonedValue).length > 0) {
				emit('update:modelValue', clonedValue);
			} else {
				emit('update:modelValue', null);
			}
		};

		function deleteValues(allPaths) {
			const newValues = { ...props.modelValue };
			allPaths.forEach(path => {
				const paths = path.split('.');

				paths.reduce((acc, key, index) => {
					if (index === paths.length - 1) {
						const dynamicValue = get(acc, `__dynamic_content__[${key}]`);
						dynamicValue !== undefined ? delete acc.__dynamic_content__ : delete acc[key];
						return true;
					}

					acc[key] = acc[key] ? { ...acc[key] } : {};

					return acc[key];
				}, newValues);
			});

			if (Object.keys(newValues).length > 0) {
				emit('update:modelValue', newValues);
			} else {
				emit('update:modelValue', null);
			}
		}

		// Provide methods for child inputs
		provide('OptionsForm', {
			getValueByPath,
			updateValueByPath,
			deleteValueByPath,
			getTopModelValueByPath,
			updateTopModelValueByPath,
			deleteTopModelValueByPath,
			modelValue: computed(() => props.modelValue),
			deleteValues,
		});

		const topOptionsForm = inject('topOptionsForm', null);
		if (!topOptionsForm) {
			provide(topOptionsForm, props.modelValue);
		}

		provide('updateValueByPath', updateValueByPath);
		provide('getValueByPath', getValueByPath);
		provide('deleteValueByPath', deleteValueByPath);

		return {
			activeResponsiveDeviceInfo,
			updateValueByPath,
			getValueByPath,
			activePseudoSelector,
			deleteValues,
			getTopModelValueByPath,
		};
	},
	computed: {
		optionsSchema() {
			const schema = {};

			Object.keys(this.schema).forEach(optionId => {
				const optionConfig = this.getProperSchema(this.schema[optionId]);
				const { dependency } = optionConfig;

				if (!dependency) {
					schema[optionId] = optionConfig;
					return;
				}

				let conditionsMet = true;

				// Check the option dependency
				dependency.forEach(element => {
					const { option, value, type, option_path: optionPath } = element;
					let optionSchema;
					let savedValue;

					// Check to see if we need to get the schema from the path
					if (optionPath) {
						optionSchema = this.getOptionSchemaFromPath(optionPath);
					} else {
						optionSchema = this.getOptionConfigFromId(option);
					}

					// Check to see if we need to get the schema from the path
					if (optionPath) {
						const defaultValue = optionSchema ? optionSchema.default : false;
						savedValue = this.getTopModelValueByPath(optionPath, defaultValue);
					} else {
						// Get the saved value from option schema
						savedValue =
							typeof this.modelValue[option] !== 'undefined' ? this.modelValue[option] : optionSchema.default;
						if (optionSchema.sync) {
							// Check to see if the option is actually a sync option
							const syncValue = this.compilePlaceholder(optionSchema.sync);
							savedValue = this.getTopModelValueByPath(syncValue, savedValue);
						}
					}

					const validationType = type || 'includes';
					if (conditionsMet && validationType === 'includes' && value.includes(savedValue)) {
						conditionsMet = true;
					} else if (conditionsMet && validationType === 'not_in' && !value.includes(savedValue)) {
						conditionsMet = true;
					} else if (conditionsMet && validationType === 'value_set' && typeof savedValue !== 'undefined') {
						conditionsMet = true;
					} else {
						conditionsMet = false;
					}
				});

				if (conditionsMet) {
					schema[optionId] = optionConfig;
				}
			});

			return schema;
		},
	},
	methods: {
		updateModelValueByPath(path, newValue) {
			const clonedValue = cloneDeep(this.modelValue || {});
			const newValues = set(clonedValue, path, newValue);

			this.$emit('update:modelValue', newValues);
		},
		setValue(optionId, newValue) {
			let newValueToSend;

			// If this is a normal option
			if (optionId) {
				// Check to see if we need to delete the value
				// || (typeof newValue === 'object' && Object.keys(newValue).length === 0) --- there is a problem with default value setting
				if (newValue === null) {
					const clonedValue = { ...this.modelValue };
					delete clonedValue[optionId];

					if (Object.keys(clonedValue).length === 0) {
						this.$emit('update:modelValue', null);
					} else {
						this.$emit('update:modelValue', clonedValue);
					}
				} else {
					this.$emit('update:modelValue', {
						...this.modelValue,
						[optionId]: newValue,
					});
				}
			} else {
				// Check to see if the value was actually deleted
				if (newValue === null || Object.keys(newValue).length === 0) {
					this.$emit('update:modelValue', null);
				} else {
					const clonedValue = { ...this.modelValue };
					Object.keys(clonedValue).reduce((acc, key, index) => {
						if (typeof newValue[key] === 'undefined') {
							delete acc[key];
						}

						return acc;
					}, clonedValue);

					this.$emit('update:modelValue', {
						...clonedValue,
						...newValue,
					});
				}
			}
		},

		getValue(optionSchema) {
			if (optionSchema.is_layout) {
				return this.modelValue;
			} else {
				return this.modelValue[optionSchema.id];
			}
		},
		getOptionConfigFromId(optionId) {
			// Check to see if the option id is a direct child
			if (this.schema[optionId] && !this.schema[optionId].is_layout) {
				return this.schema[optionId];
			} else {
				return this.findOptionConfig(this.schema, optionId);
			}
		},
		findOptionConfig(schema, searchId) {
			let optionConfig;

			for (let [optionId, optionConfig] of Object.entries(schema)) {
				if (optionConfig.is_layout && optionConfig.child_options) {
					optionConfig = this.findOptionConfig(optionConfig.child_options, searchId);
				}

				if (optionConfig && optionConfig.id === searchId) {
					return optionConfig;
				}
			}

			return optionConfig;
		},
		getOptionSchemaFromPath(optionPath) {
			const pathArray = optionPath.split('.');
			return pathArray.reduce((acc, path, index) => {
				if (acc[path]) {
					return acc[path];
				} else {
					return false;
				}
			}, this.schema);
		},
		onOptionChange(changed) {
			this.$emit('change', changed);
		},

		getProperSchema(schema) {
			const dataSetsStore = useDataSetsStore();
			// Set data sources
			if (typeof schema.data_source !== 'undefined') {
				if (schema.data_source === 'fonts') {
					schema.options = dataSetsStore.fontsListForOption;
					delete schema.data_source;
				} else if (schema.data_source === 'taxonomies') {
					schema.options = dataSetsStore.dataSets.taxonomies;
					delete schema.data_source;
				}
			}

			// Special cases for inputs
			if (schema.type === 'textarea') {
				schema.type = 'textarea';
			}

			schema = this.compilePlaceholders(schema);

			return schema;
		},

		isIterable(schema) {
			return Array.isArray(schema) || (schema === Object(schema) && typeof schema !== 'function');
		},

		compilePlaceholders(schema) {
			// Don't proceed if the object is not iterable
			if (!this.isIterable(schema)) {
				return this.compilePlaceholder(schema);
			} else {
				for (const prop in schema) {
					// Don't process sync values
					if (prop !== 'sync') {
						if (schema.hasOwnProperty(prop)) {
							schema[prop] = this.compilePlaceholders(schema[prop]);
						}
					}
				}
			}

			return schema;
		},

		compilePlaceholder(value) {
			if (typeof value !== 'string') {
				return value;
			}

			const replacements = [
				{
					search: /%%RESPONSIVE_DEVICE%%/g,
					replacement: this.replaceResponsiveDevice,
				},
				{
					search: /%%PSEUDO_SELECTOR%%/g,
					replacement: this.replacePseudoSelector,
				},
				...this.replacements,
			];

			replacements.forEach(replacementConfig => {
				value = value.replace(replacementConfig.search, replacementConfig.replacement);
			});

			return value;
		},
		/**
		 * Replace %%RESPONSIVE_DEVICE%% constant with the element UID
		 */
		replaceResponsiveDevice(match) {
			return this.activeResponsiveDeviceInfo.id;
		},
		/**
		 * Replace %%PSEUDO_SELECTOR%% constant with the element UID
		 */
		replacePseudoSelector(match) {
			return this.activePseudoSelector.id;
		},
	},
};
</script>

<style lang="scss">
.znpb-options-form-wrapper {
	display: flex;
	flex-wrap: wrap;
	// width: 100%;
	max-height: 100%;
	padding: 20px 20px 0;
}
</style>
