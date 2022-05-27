import { applyFilters } from '@/common/modules/hooks';

export const getLayoutConfigs = () => {
	return applyFilters('editor/addElementsPopup/layoutConfigs', {
		full: [
			{
				element_type: 'zion_column',
			},
		],
		'one-of-two': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '6',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '6',
					},
				},
			},
		],
		'one-of-three': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '4',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '4',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '4',
					},
				},
			},
		],
		'one-of-four': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
		],
		'one-of-five': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '1of5',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '1of5',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '1of5',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '1of5',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '1of5',
					},
				},
			},
		],
		'one-of-six': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '2',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '2',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '2',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '2',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '2',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '2',
					},
				},
			},
		],
		'4-8': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '4',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '8',
					},
				},
			},
		],
		'8-4': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '8',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '4',
					},
				},
			},
		],
		'3-9': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '9',
					},
				},
			},
		],
		'9-3': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '9',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
		],
		'3-6-3': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '6',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
		],
		'3-3-6': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '6',
					},
				},
			},
		],
		'6-3-3': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '6',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
			},
		],
		'6-3-3-3-3': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '6',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
				content: [
					{
						element_type: 'zion_column',
					},
					{
						element_type: 'zion_column',
					},
				],
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
				content: [
					{
						element_type: 'zion_column',
					},
					{
						element_type: 'zion_column',
					},
				],
			},
		],
		'3-3-3-3-6': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
				content: [
					{
						element_type: 'zion_column',
					},
					{
						element_type: 'zion_column',
					},
				],
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
				content: [
					{
						element_type: 'zion_column',
					},
					{
						element_type: 'zion_column',
					},
				],
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '6',
					},
				},
			},
		],
		'3-3-6-3-3': [
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
				content: [
					{
						element_type: 'zion_column',
					},
					{
						element_type: 'zion_column',
					},
				],
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '6',
					},
				},
			},
			{
				element_type: 'zion_column',
				options: {
					column_size: {
						default: '3',
					},
				},
				content: [
					{
						element_type: 'zion_column',
					},
					{
						element_type: 'zion_column',
					},
				],
			},
		],
	});
};
