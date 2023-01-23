import rafSchd from 'raf-schd';
import memoizeOne from 'memoize-one';

export default callbacks => {
	const memoizedMove = memoizeOne(event => {
		callbacks.onMove(event);
	});
	const move = rafSchd(memoizedMove);

	const cancel = () => {
		move.cancel();
	};

	return {
		move,
		cancel,
	};
};
