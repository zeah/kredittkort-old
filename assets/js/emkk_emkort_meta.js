(() => {

	// current input with focus (for bold and remove bold feature)
	let active = null;

	// titles, name and value for inputs
	let meta = [
		{title: 'Title', class: null, name: 'emdata[em_title]', meta: emkk_meta.meta.em_title},
		{title: 'Sortering', class: 'number', name: 'emkort_sort', meta: emkk_meta.sort[0]},
		{title: 'Info 1', class: null, name: 'emdata[em_info][]', meta: emkk_meta.meta.em_info ? emkk_meta.meta.em_info[0] : ''},
		{title: 'Info 2', class: null, name: 'emdata[em_info][]', meta: emkk_meta.meta.em_info ? emkk_meta.meta.em_info[1] : ''},
		{title: 'Info 3', class: null, name: 'emdata[em_info][]', meta: emkk_meta.meta.em_info ? emkk_meta.meta.em_info[2] : ''},
		{title: 'Aldersgrense', class: null, name: 'emdata[em_aldersgrense]', meta: emkk_meta.meta.em_aldersgrense},
		{title: 'Aldersgrense Overwrite', class: null, name: 'emdata[em_alderow]', meta: emkk_meta.meta.em_alderow},
		{title: 'Rentefri Kreditt', class: null, name: 'emdata[em_rentefrikreditt]', meta: emkk_meta.meta.em_rentefrikreditt},
		{title: 'Maks Kreditt', class: null, name: 'emdata[em_makskreditt]', meta: emkk_meta.meta.em_makskreditt},
		{title: 'Effektiv Rente Eksempel', class: null, name: 'emdata[em_effrente]', meta: emkk_meta.meta.em_effrente},
		{title: 'Blurb', class: null, name: 'emdata[em_blurb]', meta: emkk_meta.meta.em_blurb},
		{title: 'Les Mer Lenke', class: null, name: 'emdata[em_lesmer]', meta: emkk_meta.meta.em_lesmer},
		{title: 'Søk Nå Lenke', class: null, name: 'emdata[em_sokna]', meta: emkk_meta.meta.em_sokna},
	]; 

	// locating where to insert html
	let container = document.querySelector('.emkort-meta-container');
	let inputContainer = document.createElement('div');

	let button = (text) => {
		let b = document.createElement('button');
		b.setAttribute('type', 'button');

		b.appendChild(document.createTextNode(text));

		return b;
	}

	// creating element with container, title, input, counter elements
	let input = (title, name, meta = null, cl = null) => {
		// container
		let c = document.createElement('div');
		c.classList.add('emkort-admin-container')

		// title element
		let t = document.createElement('div');
		t.classList.add('emkort-admin-title');
		t.appendChild(document.createTextNode(title))
		c.appendChild(t);

		// input
		let i = document.createElement('input');
		i.classList.add('emkort-input');
		
		// if custom input type 
		if (!cl) i.setAttribute('type', 'text');
		else {
			i.setAttribute('type', cl);

			if (cl == 'number')
				i.setAttribute('step', '0.1');
		}

		// name to be saved as
		i.setAttribute('name', name);

		// if found in database
		if (meta) i.setAttribute('value', meta);
		
		// appends input to container
		c.appendChild(i);

		// counter
		let s = document.createElement('span');
		s.classList.add('emkort-counter');
		s.appendChild(document.createTextNode(meta ? meta.length : '0'));
		
		if (title != 'Sortering') c.appendChild(s);

		// updating counter
		i.addEventListener('input', (e) => s.innerHTML = e.target.value.length);

		// stores active input (for bold feature)
		i.addEventListener('focus', (e) => active = e.target);

		return c;
	}
	
	let buttonContainer = document.createElement('div');

	// "add bold" button
	let fat = document.createElement('button');
	fat.setAttribute('type', 'button');
	fat.appendChild(document.createTextNode('Bold'));

	fat.addEventListener('click', () => {
		if (window.getSelection().toString()) {
			let sel = window.getSelection().toString();
			active.value = active.value.replace(new RegExp(sel), '[b]'+sel+'[/b]');
			active.focus();
		}
	});

	// "remove bold" button
	let rfat = document.createElement('button');
	rfat.setAttribute('type', 'button');
	rfat.appendChild(document.createTextNode('Remove Bold'));

	rfat.addEventListener('click', () => active.value = active.value.replace(/\[\/?b\]/gi, ''));


	// copy button
	let copy = button('Copy');
	copy.classList.add('emkort-button-copy');
	copy.setAttribute('title', 'Copies Saved Data To Clipboard');
	copy.addEventListener('click', () => {

		let temp = document.createElement('input');
		temp.setAttribute('value', JSON.stringify(meta));

		container.appendChild(temp);
		temp.select();
		document.execCommand('copy');
		container.removeChild(temp);
	});

	let paste = button('Paste');
	paste.classList.add('emkort-button-copy');
	paste.setAttribute('title', 'Paste Data from Windows Clipboard');
	paste.addEventListener('click', () => {
		let temp = document.createElement('input');
		temp.classList.add('emkort-copy-input');
		temp.setAttribute('placeholder', 'ctrl+v here');

		temp.addEventListener('input', (e) => {
			// console.log('hi '+e.target.value);

			if (e.target.value.includes('[{"title":"Title"')) {
				console.log('ya');
				meta = JSON.parse(e.target.value);

				inputContainer.innerHTML = '';

				for (let v of meta) inputContainer.appendChild(input(v.title, v.name, v.meta, v.class));


				container.removeChild(temp);
			}
			


		});
		container.insertBefore(temp, inputContainer);
	});


	// adding buttons to container
	buttonContainer.appendChild(fat);
	buttonContainer.appendChild(rfat);
	buttonContainer.appendChild(copy);
	buttonContainer.appendChild(paste);

	container.appendChild(buttonContainer);

	// let inputContainer = document.createElement('div');
	// adding inputs to container
	for (let v of meta)
		inputContainer.appendChild(input(v.title, v.name, v.meta, v.class));

	container.appendChild(inputContainer);

	// console.log(emkk_meta);
})();

// CSS SPRITE SETTINGS
(() => {
	let set = new Set(['', 'shell_mastercard', '365privat', 'sas_eurobonus', 'ya_bank', 
						   'bank_norwegian', 'credits_visa', 'aros_finans', 'remember_black',
						   'santander_gold', 'santander_manu', 'santander_flexi', 'komplett_mastercard',
						   'ikano_bank', 'circle_k', 'yx_visa', 'santander_red']);

	let meta = emkk_meta.meta.em_sprite;
	let cImage = meta;

	let container = document.querySelector('.emkort-sprite-container');

	let image = document.createElement('div');
	image.classList.add('em-sprite');
	image.style.borderRadius = '8px';

	if (meta) 
		image.classList.add('sprite-'+meta);

	container.appendChild(image);

	let info = document.createElement('div');
	info.appendChild(document.createTextNode('If selected, then featured image will be overwritten.')); 

	let option = (v) => {
		let e = document.createElement('option');
		e.setAttribute('value', v);
		e.appendChild(document.createTextNode(v ? v.replace(/_/g, ' ') : 'No Sprite'));
		if (v == meta)
			e.setAttribute('selected', '');

		return e;
	}


	let s = document.createElement('select');
	s.setAttribute('name', 'emdata[em_sprite]');

	s.addEventListener('change', () => {
		console.log('hi '+s.value);
		image.classList.remove('sprite-'+cImage);
		cImage = s.value;
		image.classList.add('sprite-'+s.value);
	});

	for (let se of set)
		s.appendChild(option(se));

	container.appendChild(info);
	container.appendChild(s);
})();