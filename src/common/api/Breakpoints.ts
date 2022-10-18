import { getService } from './ZionService';

export function saveBreakpoints(breakpoints) {
	return getService().post('breakpoints', breakpoints);
}

export function getBreakpoints() {
	return getService().get('breakpoints');
}
