import { getService } from './ZionService';

export function getGoogleFonts() {
	return getService().get('google-fonts');
}
