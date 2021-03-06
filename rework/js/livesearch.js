/*

	TODO:
		(1) Highlight next author on down arrow key
		(2) Move Author to inbox if fenter in pressed

*/

function livesearch(objId, script, args, vals) {
	if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  }
  else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById(objId).innerHTML=xmlhttp.responseText;
    }
  }
  for (var i = 0, len = args.length, scriptName = script; i < len; i++) {
  	scriptName += (i == 0 ? "?" : "&");
  	scriptName += args[i] + "=" + vals[i];
  }
  xmlhttp.open("GET", scriptName, true);
  xmlhttp.send();
}

function showResult(obj, str, resultId) {
	var tr = obj.parentNode.parentNode;
	var oldid = tr.getAttribute('id');
	var newid = oldid + "list";
	var args = ["q", "id", "input"];
	var vals = [str, oldid, resultId];
	livesearch(newid, "php/livesearch.php", args, vals);
  document.getElementById(newid).style.border="1px solid #A5ACB2";
}

function fillbox(id, str, inputId) {
	var input = document.getElementById(inputId);
	input.value = str;
	var div = id.getElementsByTagName("div");
	div[0].innerHTML = ""; 
}

function myKeyPress(e){
	var keynum;
	if(window.event){ // IE					
		keynum = e.keyCode;
	} else if(e.which){ // Netscape/Firefox/Opera					
		keynum = e.which;
	}
	if (keynum == '40') {
		// TODO: Highlight next author in list
	}
	if (keynum == '13') {
		// TODO: Fill box with highlighted author
		return false;
	}
}

function getPubsByYear(year, selectId) {
	if (year == '0000') {
		year = document.getElementById('year').value;
	}
	var args = ["year"];
	var vals = [year];
	livesearch(selectId, "php/livepubsearch.php", args, vals);
}

function fillModificationForm(pubTitle, selectId) {
	var args = ["id"];
	var vals = [pubTitle];
	livesearch(selectId, "php/livepubsearchbytitle.php", args, vals);
}

function deleteentry(selectId, pubtitle) {
	var r = confirm("This will delete all record of this entry:\n" + pubtitle + "\nAre you sure you want to proceed?");
	if (r == true) {
		var args = ["pubtitle"];
		var vals = [pubtitle];
		livesearch(selectId, "php/deletepublication.php", args, vals);
	}
}

function deletematches(selectId, pubtitle, pubid) {
	var r = confirm("This will search for any exact duplicates and delete them:\n" + pubtitle + "\nAre you sure you want to proceed?");
	if (r == true) {
		var args = ["pubtitle", "pubid"];
		var vals = [pubtitle, pubid];
		livesearch(selectId, "php/deletepublicationmatches.php", args, vals);
	}
}
	