
function bhab_change_type(el, type) {
	var colorPicker = el.parentNode.parentNode.children[3];

	if (el.checked && type == 'button')
		colorPicker.style.display = 'block';
	else
		colorPicker.style.display = 'none';

	bhab_build_preview(el);
}

function bhab_build_preview(el) {
	var p = el.parentNode.parentNode;
	
	var name = p.children[0].children[1].value;
	var email = p.children[1].children[1].value;
	var type = (p.children[2].children[0].checked) ? 'link' : 'button';
	var buttonColor = p.children[3].children[1].value;
	var preview = p.children[4].children[1];
	
	// show preview
	p.children[4].style.display = 'block';
	
	preview.innerHTML = '';
	var url = 'http://buyhimabeer.com/buy?name=' + encodeURIComponent(name) + '&email=' + encodeURIComponent(email);
	var a = document.createElement('a');
	a.setAttribute('target', '_blank');
	a.setAttribute('href', url);
	
	if (type == 'link') {
		a.setAttribute('style', 'display: block; text-align: center;');
		a.innerHTML = 'Buy ' + name + ' a Beer';
		preview.appendChild(a);
	}
	else if (type == 'button') {
		var b = document.createElement('button');
		b.setAttribute('class', 'bhab-btn bhab-btn-' + buttonColor);
		b.setAttribute('style', 'display: block; margin: auto;');
		
		var icon = document.createElement('div');
		icon.setAttribute('class', 'bhab-btn-icon');
		
		var text = document.createElement('p');
		text.setAttribute('class', 'bhab-btn-text');
		text.innerHTML = 'Buy ' + name + ' a Beer';

		b.appendChild(icon);
		b.appendChild(text);
		a.appendChild(b);
		preview.appendChild(a);
	}
}

