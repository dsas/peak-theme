/**
 * Easter egg: a man on the homepage hills throws sushi at the rider as they pass.
 * Loaded by rider.js only for browsers that have visited the homepage with #sushi.
 */
(() => {
	const scene = document.querySelector('.peak-hero__scene');
	const rider = scene && scene.querySelector('.peak-rider');
	if (!rider || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
	const ridge = scene.querySelectorAll('.peak-hero__layer--near path')[1];
	const total = ridge.getTotalLength();

	// Ridge point (scene coordinates) at a given screen x.
	const box = () => scene.getBoundingClientRect();
	const pointAt = (len) => new DOMPoint(ridge.getPointAtLength(len).x, ridge.getPointAtLength(len).y).matrixTransform(ridge.getScreenCTM());
	function groundAt(x) {
		const b = box();
		let lo = 0, hi = total;
		for (let i = 0; i < 20; i++) {
			const mid = (lo + hi) / 2;
			if (pointAt(mid).x - b.left < x) lo = mid; else hi = mid;
		}
		return pointAt(lo).y - b.top;
	}

	// The thrower, facing left, a little way past the rider's hilltop.
	const man = document.createElement('div');
	man.className = 'peak-thrower';
	man.innerHTML = `<svg viewBox="0 0 30 46" fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
		<path d="M13 46 15 33 17 46" fill="none" stroke-width="2.4"/>
		<path d="M10 34 Q15 36 20 34 L19.5 20 Q15 17 10.5 20Z" stroke="none"/>
		<circle cx="15" cy="11" r="5" stroke="none"/>
		<g class="peak-thrower__arm"><path d="M15 21 L24 21" fill="none" stroke-width="2.4"/></g>
	</svg>`;
	scene.appendChild(man);

	// Rice, a topping and a nori band, for the nigiri.
	const nigiri = (topping) => `<svg viewBox="0 0 16 10"><rect x="1" y="4" width="14" height="5.5" rx="2.6" fill="#f7f2e7"/>${topping}<rect x="6.6" y="0.6" width="2.8" height="9" rx="0.6" fill="#1f2a24"/></svg>`;
	const SUSHI = {
		salmon: nigiri(`<rect x="0" y="1" width="16" height="5" rx="2.5" fill="#f2875a"/><path d="M4 2 6 5.5M8 1.5 10 5.5M12 2 13.5 5" stroke="#fbd2bd" stroke-width="0.8" stroke-linecap="round"/>`),
		tuna: nigiri(`<rect x="0" y="1" width="16" height="5" rx="2.5" fill="#c63a4a"/><path d="M2.5 2.4H13" stroke="#e2707c" stroke-width="0.7" stroke-linecap="round"/>`),
		tamago: nigiri(`<rect x="0.3" y="0.4" width="15.4" height="5.6" rx="1.6" fill="#f3cd55"/><path d="M1.5 3.4H14.5" stroke="#e2b23a" stroke-width="0.5"/>`),
		ebi: nigiri(`<path d="M0 3 -2.6 0.8 -2.4 5.4Z" fill="#e2603c"/><rect x="0" y="1" width="16" height="5" rx="2.5" fill="#f6a378"/><path d="M3 1.4V5.6M6 1.2V5.8M11 1.2V5.8M13.8 1.6V5.4" stroke="#fde3d3" stroke-width="0.9" stroke-linecap="round"/>`),
		maki: `<svg viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="#1f2a24"/><circle cx="6" cy="6" r="4.9" fill="#f7f2e7"/><circle cx="6" cy="6" r="2" fill="#6fae5c"/><circle cx="6.7" cy="5.4" r="0.8" fill="#f2875a"/></svg>`,
	};
	// One of each, in a random order.
	const order = Object.keys(SUSHI).sort(() => Math.random() - 0.5);


	function placeMan() {
		const b = box(), x = b.width * 0.9;
		const w = man.offsetWidth, h = man.offsetHeight;
		man.style.transform = `translate(${x - w / 2}px, ${groundAt(x) - h + 2}px)`;
		return { x, y: groundAt(x) - h * 0.55, h };
	}

	function riderHead() {
		const m = rider.style.transform.match(/translate\(([-\d.]+)px, ([-\d.]+)px\)/);
		if (!m) return null;
		const w = rider.offsetWidth, h = rider.offsetHeight;
		return { x: +m[1] + w * 0.62, y: +m[2] + h * 0.15 };
	}

	// A piece of sushi that homes in on the rider along an arc, bounces off and lands on the hill.
	function throwSushi(kind) {
		const hand = placeMan();
		man.classList.add('is-throwing');
		setTimeout(() => man.classList.remove('is-throwing'), 260);
		const s = document.createElement('div');
		s.className = 'peak-sushi peak-sushi--' + kind;
		s.innerHTML = SUSHI[kind];
		scene.appendChild(s);
		const sw = s.offsetWidth;
		const flight = 850, start = performance.now(), spin = (Math.random() - 0.5) * 2;
		let hit = null;
		const step = (now) => {
			const t = Math.min(1, (now - start) / flight);
			const target = riderHead();
			if (!hit) {
				const x = hand.x + (target.x - hand.x) * t;
				const y = hand.y + (target.y - hand.y) * t - hand.h * 2.2 * 4 * t * (1 - t);
				s.style.transform = `translate(${x - sw / 2}px, ${y}px) rotate(${t * 540 * spin}deg)`;
				if (t === 1) hit = { x, y, at: now };
				requestAnimationFrame(step);
				return;
			}
			// Bounce back towards the thrower and drop to the ground.
			const u = (now - hit.at) / 1000;
			const x = hit.x + 55 * u;
			const ground = groundAt(x) - sw * 0.55;
			const y = Math.min(ground, hit.y - 90 * u + 420 * u * u);
			s.style.transform = `translate(${x - sw / 2}px, ${y}px) rotate(${(y >= ground ? 0 : 540 * spin + u * 900)}deg)`;
			if (y < ground) {
				requestAnimationFrame(step);
				return;
			}
			// Landed: remember where along the hill, so it stays on the ridge when the window resizes.
			s.dataset.at = x / box().width;
			landed.push(s);
		};
		requestAnimationFrame(step);
	}

	const landed = [];
	function placeLanded() {
		const width = box().width;
		landed.forEach((s) => {
			const x = s.dataset.at * width, sw = s.offsetWidth;
			s.style.transform = `translate(${x - sw / 2}px, ${groundAt(x) - sw * 0.55}px)`;
		});
	}

	// Start throwing once the rider is a quarter of the way across; one of each kind, then stop.
	let thrown = 0;
	const watch = () => {
		const head = riderHead();
		if (head && head.x > box().width * 0.25 && thrown < order.length) {
			throwSushi(order[thrown++]);
			setTimeout(() => requestAnimationFrame(watch), 950);
			return;
		}
		if (thrown < order.length) requestAnimationFrame(watch);
	};
	placeMan();
	requestAnimationFrame(watch);
	addEventListener('resize', () => {
		placeMan();
		placeLanded();
	});
})();
