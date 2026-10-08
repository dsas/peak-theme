/**
 * Homepage: a cyclist rides in along the near hill once when the page loads and stops on a hilltop.
 * The hills stretch to fit the hero, so the rider is a separate element placed on the ridge line.
 * Reduced motion: the rider is already parked on the hilltop. Without JS there's no rider.
 */
(() => {
	const scene = document.querySelector('.peak-hero__scene');
	const near = scene && scene.querySelector('.peak-hero__layer--near');
	if (!near) return;
	const NS = 'http://www.w3.org/2000/svg';

	// The ridge: the near hill's outline without its closing edges.
	const ridge = document.createElementNS(NS, 'path');
	ridge.setAttribute('d', near.querySelector('path').getAttribute('d').split(' V')[0]);
	ridge.setAttribute('fill', 'none');
	ridge.style.visibility = 'hidden';
	near.appendChild(ridge);
	const total = ridge.getTotalLength();

	const rider = document.createElement('div');
	rider.className = 'peak-rider';
	rider.innerHTML = `<svg viewBox="0 0 48 40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
		<g class="peak-rider__wheel" data-x="10"><circle r="8"/><path d="M-8 0H8M0-8V8" stroke-width="1"/></g>
		<g class="peak-rider__wheel" data-x="38"><circle r="8"/><path d="M-8 0H8M0-8V8" stroke-width="1"/></g>
		<path d="M10 31 20 18 22 31Z M20 18 33 16 22 31 M33 16 38 31 M33 16 32 12 35 12 M18 17H22"/>
		<polyline class="peak-rider__leg" opacity="0.55"/>
		<path class="peak-rider__crank"/>
		<path d="M20 16 28 8 M28 8 34 12"/>
		<circle cx="30.5" cy="4.5" r="3.2" fill="currentColor" stroke="none"/>
		<polyline class="peak-rider__leg"/>
		<circle class="peak-rider__lamp" cx="36" cy="12.5" r="1.6" stroke="none"/>
	</svg>`;
	scene.appendChild(rider);

	const wheels = rider.querySelectorAll('.peak-rider__wheel');
	const [farLeg, nearLeg] = rider.querySelectorAll('.peak-rider__leg');
	const crank = rider.querySelector('.peak-rider__crank');
	const hip = [20, 16], crankCentre = [22, 31], thigh = 9.5, shin = 9.5;

	// A leg from the hip to the pedal at crank angle `a`, with the knee bent forwards.
	function leg(a) {
		const pedal = [crankCentre[0] + 4 * Math.cos(a), crankCentre[1] + 4 * Math.sin(a)];
		const dx = pedal[0] - hip[0], dy = pedal[1] - hip[1], d = Math.hypot(dx, dy);
		// Clamped: at the bottom of the stroke the pedal is just beyond the leg's reach.
		const bend = Math.acos(Math.min(1, (thigh * thigh + d * d - shin * shin) / (2 * thigh * d)));
		const b = Math.atan2(dy, dx) - bend;
		return { pedal, points: `${hip} ${hip[0] + thigh * Math.cos(b)},${hip[1] + thigh * Math.sin(b)} ${pedal}` };
	}

	// A point on the ridge in screen coordinates. Before the ridge starts, carry on along its first
	// slope, so the ride can begin off-screen.
	function toScreen(len) {
		const m = ridge.getScreenCTM();
		const at = (l) => {
			const p = ridge.getPointAtLength(l);
			return new DOMPoint(p.x, p.y).matrixTransform(m);
		};
		if (len >= 0) return at(len);
		const a = at(0), b = at(4), s = Math.hypot(b.x - a.x, b.y - a.y), px = len * m.a;
		return new DOMPoint(a.x + ((b.x - a.x) / s) * px, a.y + ((b.y - a.y) / s) * px);
	}

	// Stop on the highest point of the ridge's right-hand half.
	let stop = total / 2, highest = Infinity;
	for (let l = total * 0.45; l < total * 0.9; l += 4) {
		const y = ridge.getPointAtLength(l).y;
		if (y < highest) {
			highest = y;
			stop = l;
		}
	}

	let len = stop;

	function draw() {
		const a = toScreen(len), b = toScreen(len + 4);
		const box = scene.getBoundingClientRect();
		const w = rider.offsetWidth, h = rider.offsetHeight;
		const angle = Math.atan2(b.y - a.y, b.x - a.x);
		rider.style.transform = `translate(${a.x - box.left - w / 2}px, ${a.y - box.top - h + 1}px) rotate(${angle}rad)`;

		// Wheels turn with the distance travelled; the cranks at half that speed.
		const turn = (a.x - box.left) / ((w * 8) / 48);
		wheels.forEach((g) => g.setAttribute('transform', `translate(${g.dataset.x} 31) rotate(${(turn * 180) / Math.PI})`));
		const n = leg(turn / 2), f = leg(turn / 2 + Math.PI);
		nearLeg.setAttribute('points', n.points);
		farLeg.setAttribute('points', f.points);
		crank.setAttribute('d', `M${n.pedal}L${f.pedal}`);
	}

	// One ride: in from just off the left edge, easing to a stop on the hilltop.
	if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
		const scale = ridge.getScreenCTM().a;
		const from = -30 / scale;
		const duration = Math.min(11000, Math.max(5000, (((stop - from) * scale) / 75) * 1000));
		const ease = (t) => (t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2);
		len = from;
		let start = null;
		const step = (now) => {
			start ??= now + 150;
			const t = Math.min(1, Math.max(0, (now - start) / duration));
			len = from + (stop - from) * ease(t);
			draw();
			if (t < 1) requestAnimationFrame(step);
		};
		requestAnimationFrame(step);
	}

	addEventListener('resize', () => requestAnimationFrame(draw));
	draw();
})();
