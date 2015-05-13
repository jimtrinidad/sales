//Call at the time of upload
var basepath = '';
function upload(fileObj){
	var par = window.document;
	var frm = fileObj.form;
	var div_id = parseInt(Math.random() * 100000);

	// hide old iframe
	var iframes = par.getElementById('iframe_container').getElementsByTagName('iframe');
	var iframe = iframes[iframes.length - 1];
	//iframe.className = 'hidden';
	//iframe.style.height = '0';
	iframe.style.display = 'none';
	
	// hide old image
	var e = par.getElementById('images_container');
	var imgs = par.getElementById('images_container').getElementsByTagName('div');
	for(var i = 0;i < imgs.length; i++)
	{
		var a = imgs[i];
		e.removeChild(a);
	}

	// create new iframe
	var new_iframe = par.createElement('iframe');
	new_iframe.src = basepath + 'assets/pm-upload.php';
	new_iframe.frameBorder = '0';
	new_iframe.scrolling = 'no';
	new_iframe.style.height = '40px';
	par.getElementById('iframe_container').appendChild(new_iframe);
	
	// add image progress
	var images = par.getElementById('images_container');
	var new_div = par.createElement('div');
	new_div.id = div_id;
	
	var new_img = par.createElement('img');
	new_img.src = 'images/indicator2.gif';
	new_img.style.marginLeft = '33px';
	new_img.style.marginTop = '50px';
	new_div.appendChild(new_img);
	images.appendChild(new_div);
	
	var errorDiv = par.getElementById('error');
	errorDiv.innerHTML = "";
	errorDiv.style.display = 'none';
	
	// send
	frm.div_id.value = div_id;
	setTimeout(frm.submit(),5000);
}

//Call when upload completed
function setUploadedImage(imgSrc, fileTempName, divId) {
	var par = window.document;
		
	var images = par.getElementById('images_container');

	var imgdiv = par.getElementById(divId);
	var image = imgdiv.getElementsByTagName('img')[0];
	imgdiv.removeChild(image);
	
	var image_new = par.createElement('img');
	image_new.src = basepath + 'assets/' + imgSrc;
	image_new.className = 'pic';

	imgdiv.appendChild(image_new);
	
}

// call when error occurred at the time of upload
function uploadError(divId, oName) {
	var par = window.document;
	var images = par.getElementById('images_container');
	var imgdiv = par.getElementById(divId);
	images.removeChild(imgdiv);
	var errorDiv = par.getElementById('error');
	errorDiv.innerHTML = oName + " has invalid file type.";
	errorDiv.style.display = 'inline';
	
}