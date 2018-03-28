(() => {

	// where to insert html
	let c = document.querySelector('#screen-meta-links');

	let list = [];

	// if element is shown on screen
	let show = false;

	// helper function
	let text = (text) => document.createTextNode(text);

	// span creator to contain a selected kredittkort
	let span = (v) => {
		let e = document.createElement('span');
		e.classList.add('emkort-span');

		e.appendChild(text(','+v));

		e.addEventListener('click', () => {
			e.parentNode.removeChild(e)
			removecomma();

			let i = list.indexOf(v);
			if (i > -1)
				list.splice(i, 1);
		});

		return e;
	}

	// removing comma from first item
	let removecomma = () => {
		let ee = document.querySelector('.emkort-span');
		
		if (ee && ee.innerHTML.charAt(0) == ',')
			ee.innerHTML = ee.innerHTML.substring(1);
	}

	// container element
	let container = document.createElement('div');
	container.classList.add('emkort-container');

	// close window button
	let cbutton = document.createElement('button');
	cbutton.setAttribute('type', 'button');
	cbutton.classList.add('emkort-xbutton');
	cbutton.appendChild(text('X'));
	cbutton.addEventListener('click', (e) => {
		e.target.parentNode.parentNode.removeChild(container);
		show = false;
	});
	container.appendChild(cbutton);

	let copybutton = document.createElement('button');
	copybutton.setAttribute('type', 'button');
	copybutton.classList.add('emkort-xbutton');
	copybutton.appendChild(text('Copy'));
	copybutton.addEventListener('click', (e) => {
		let i = document.createElement('input');
		i.setAttribute('type', 'text');
		i.setAttribute('value', '[emkort name="'+list.toString()+'"]');

		c.appendChild(i);
		
		i.select();
		document.execCommand('copy');
		c.removeChild(i);
		// make hidden or invisible input with list as value then select and execCommand('copy')
		// add notification that it has been copied to clipboard

	});
	container.appendChild(copybutton);

	// initial state of element
	container.appendChild(text('[emkort name="'));
	container.appendChild(text('"]'));

	// getting all the buttons for adding
	let buttons = document.querySelectorAll('.emkort-button');
	for (let b of buttons)
		b.addEventListener('click', (e) => {
			// shows container
			if (!show)
				c.appendChild(container);
			show = true;

			// removes "] 
			container.removeChild(container.lastChild);

			// adds span with chosen kredittkort
			container.appendChild(span(e.target.getAttribute('data')));
			
			// adds "] back to the end
			container.appendChild(text('"]'));

			list.push(e.target.getAttribute('data'));
			// location.search += '&emkort='+e.target.getAttribute('data');
			
			// checks and fixes comma on first item
			removecomma();
		});


})();