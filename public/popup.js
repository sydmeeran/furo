function popup(url1, url2, url3, text = 'Click me!', div_id = 'body', remove = false) {
	let popup_open = true;
	let div = document.querySelector(div_id)
	let lnk = document.createElement("link")
	lnk.href = url2
	lnk.target = "_blank"

	let btn = document.createElement("button")
	btn.innerText = text
	btn.onclick = () => {
		if(popup_open) {
			// First popup
			if(url1) {
				window.open(url1, '_blank');
			}
			// Second popup
			if(url2) {
				lnk.click()
			}
		} else {
			// Second click (download link)
			if(url3) {
				window.open(url3, '_blank');
			}
		}

		// Remove onclick event
		if(remove) {
			btn.onclick = null;
			// evt.target.onclick = null;
		}

		// Change click location
		popup_open = false;
	}

	div.appendChild(lnk)
	div.appendChild(btn)
}

/*
window.onload = function() {
	// Open 2 popup windows on button click
	popup('https://rog.asus.com/pl', 'https://razer.com?q=download', 'https://www.falcon-nw.com', 'Open popups on click! Then download on click!', '#div');
}
*/