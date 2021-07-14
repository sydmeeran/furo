function IsBlank(str) {
	str = str.trim()
	return (!str || /^\s*$/.test(str) || str.length === 0)
}

function IsValidUrl(url) {
	var re = /(http|https):\/\/(\w+:{0,1}\w*)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/
	return re.test(url)
}

// Validate email
function IsValidEmail(email) {
	const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
	return re.test(email)
}

// Validate alias
function IsValidUsername(str)
{
	// const re = /^\w+$/;	// str with underscore
	const re = /^[a-zA-z]{1}[A-Za-z0-9_.]+$/
	return re.test(str)
}