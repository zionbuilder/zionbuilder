import { getService } from './ZionService';

export function saveBreakpoints(breakpoints: Breakpoint[]) {
	return getService().post('breakpoints', breakpoints);
}

export function getBreakpoints() {
	return getService().get('breakpoints');
}
