(() => {


	let c = document.querySelector('#screen-meta-links');
	
	let show = false;
	// let first = true;

	let text = (text) => document.createTextNode(text);

	let span = (v) => {
		let e = document.createElement('span');
		e.classList.add('emkort-span');

		e.appendChild(text(v));

		e.addEventListener('click', () => {
			e.parentNode.removeChild(e)
			removecomma();
		});

		return e;
	}

	let removecomma = () => {
		let ee = document.querySelector('.emkort-span');
		
		if (ee && ee.innerHTML.charAt(0) == ',')
			ee.innerHTML = ee.innerHTML.substring(1);
	}

	let container = document.createElement('div');
	container.classList.add('emkort-container');

	let cbutton = document.createElement('button');
	cbutton.setAttribute('type', 'button');
	cbutton.classList.add('emkort-xbutton');
	cbutton.appendChild(text('X'));
	cbutton.addEventListener('click', (e) => {
		e.target.parentNode.parentNode.removeChild(container);
		show = false;


	});
	container.appendChild(cbutton);



	container.appendChild(text('[emkort name="'));
	container.appendChild(text('"]'));


	let buttons = document.querySelectorAll('.emkort-button');
	for (let b of buttons)
		b.addEventListener('click', (e) => {
			if (!show)
				c.appendChild(container);

			show = true;

			container.removeChild(container.lastChild);

			container.appendChild(span(','+e.target.getAttribute('data')));
			container.appendChild(text('"]'));
			
			removecomma();
		});
})();