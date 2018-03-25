(() => {
	// sokelenke, lesmerlenke, info 1+++, aldersgrense, alderow, maks kreditt, rentefri kreditt, eff rente, blurb

	let active = null;

	let meta = [
		{title: 'Sortering', name: 'emkort_sort', meta: emkk_meta.sort[0]},
		{title: 'Info 1', name: 'emdata[em_info][]', meta: emkk_meta.meta.em_info ? emkk_meta.meta.em_info[0] : ''},
		{title: 'Info 2', name: 'emdata[em_info][]', meta: emkk_meta.meta.em_info ? emkk_meta.meta.em_info[1] : ''},
		{title: 'Info 3', name: 'emdata[em_info][]', meta: emkk_meta.meta.em_info ? emkk_meta.meta.em_info[2] : ''},
		{title: 'Aldersgrense', name: 'emdata[em_aldersgrense]', meta: emkk_meta.meta.em_aldersgrense},
		{title: 'Aldersgrense Overwrite', name: 'emdata[em_alderow]', meta: emkk_meta.meta.em_alderow},
		{title: 'Rentefri Kreditt', name: 'emdata[em_rentefrikreditt]', meta: emkk_meta.meta.em_rentefrikreditt},
		{title: 'Maks Kreditt', name: 'emdata[em_makskreditt]', meta: emkk_meta.meta.em_makskreditt},
		{title: 'Effektiv Rente Eksempel', name: 'emdata[em_effrente]', meta: emkk_meta.meta.em_effrente},
		{title: 'Blurb', name: 'emdata[em_blurb]', meta: emkk_meta.meta.em_blurb},
		{title: 'Les Mer Lenke', name: 'emdata[em_lesmer]', meta: emkk_meta.meta.em_lesmer},
		{title: 'Søk Nå Lenke', name: 'emdata[em_sokna]', meta: emkk_meta.meta.em_sokna},
	]; 

	let container = document.querySelector('.emkort-meta-container');

	let input = (title, name, meta = null) => {
		let c = document.createElement('div');
		c.classList.add('emkort-container')

		let t = document.createElement('div');
		t.classList.add('emkort-title');
		t.appendChild(document.createTextNode(title))
		c.appendChild(t);

		let i = document.createElement('input');
		i.classList.add('emkort-input');
		i.setAttribute('type', 'text');
		i.setAttribute('name', name);
		if (meta) i.setAttribute('value', meta);
		c.appendChild(i);

		let s = document.createElement('span');
		s.classList.add('emkort-counter');
		s.appendChild(document.createTextNode(meta ? meta.length : '0'));
		c.appendChild(s);

		i.addEventListener('input', (e) => s.innerHTML = e.target.value.length);
		i.addEventListener('focus', (e) => active = e.target);

		return c;
	}

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

	let rfat = document.createElement('button');
	rfat.setAttribute('type', 'button');
	rfat.appendChild(document.createTextNode('Remove Bold'));

	rfat.addEventListener('click', () => active.value = active.value.replace(/\[\/?b\]/gi, ''));

	container.appendChild(fat);
	container.appendChild(rfat);

	for (let v of meta)
		container.appendChild(input(v.title, v.name, v.meta));

	console.log(emkk_meta);
})();
