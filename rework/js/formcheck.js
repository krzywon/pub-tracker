// Checks to see if javascript is enabled. If so, changes the form 
// 	to be more dynamic. Otherwise, defaults to 100% PHP checking.
function jScriptCheck() {
	document.getElementById("jscheck").value = 'js';
	var frm = document.getElementById("myform");
	if(frm) {
		frm.action = 'php/pubaddfinal.php';
	}
	var sbmt = document.getElementById("submit_btn");
	if (sbmt) {
		sbmt.value = "Add Publication";
	}
	var buttons = document.getElementById("addremove").getElementsByTagName('input');
	for (var i = 0; i < buttons.length; ++i) {
		buttons[i].disabled = false;
	}
}

function addFields(id1, id2, name) {
	var edited = document.getElementById(id1);
	var number = parseInt(edited.value, 10);
	var change = document.getElementById(id2);
	while (change.firstChild) {
		change.removeChild(change.firstChild);
	}
	for (var x = 1; x <= number; x++) {
		var newname = name + x;
		var label = document.createElement('label');
		var inputnew = document.createElement('input');
		var makeBreak = document.createElement('br');
		var labelname = 'label' + newname;
		label.setAttribute('for', newname);
		label.setAttribute('id', labelname);
		inputnew.setAttribute('id', newname);
		inputnew.setAttribute('name', newname);
		inputnew.setAttribute('type', 'text');
		change.appendChild(label);
		change.appendChild(inputnew);
		change.appendChild(makeBreak);
		document.getElementById(labelname).innerHTML = newname + ": ";
	}
}

function validateJName(jid1, jid2) {
	var jseed = document.getElementById(jid1);
	var jname = jseed.options[jseed.selectedIndex].text;
	var changes = document.getElementById(jid2);
	if (jname === "Other...") {
		changes.className = 'visible';
	}
	else {
		changes.className = 'hidden';
	}
}

function pdfCheck(pdfid1, pdfid2) {
	var pdf = document.getElementById(pdfid1);
	var pdfchange = document.getElementById(pdfid2);
	if (pdf.checked) {
		pdfchange.className = 'visible';
	}
	else {
		pdfchange.className = 'hidden';
	}
}

// Script that checks all information entered meets the required format.
function verifyForm() {
	var errors = '';
	var checkForNull = new Array();
	var errorArray = ["<li>You omitted the number of authors.<\li>", "<li>You omitted the title.<\li>", "<li>You omitted the year.<\li>", "<li>You omitted the volume number.<\li>", "<li>You omitted the issue number.<\li>", "<li>You omitted the first page number.<\li>", "<li>You did not give a filename for the PDF.<\li>", "<li>You did not choose an insturment or IGOR.<\li>", "<li>You did not choose which sample environment were used.<\li>"];
	checkForNull[0] = document.getElementById("auth").getElementsByTagName("tr");
	checkForNull[1] = document.getElementById("title").value;
	checkForNull[2] = document.getElementById("year").value;
	checkForNull[3] = document.getElementById("volume").value;
	checkForNull[4] = document.getElementById("issue").value;
	checkForNull[5] = document.getElementById("firstpage").value;
	for (var x = 0; x < checkForNull[0].length; ++x) {
		var item = checkForNull[0][x];
		var authorI = item.getElementsByTagName("input")[0].value;
		if (!authorI) {
			var authid = item.getAttribute('id');
			errors = errors + "<li>You did not give the name of " + authid + ".<\li>";
		}
	}
	for (var i = 0; i < 2; i++) {
		if (!checkForNull[i]) {
			errors = errors + errorArray[i];
		}
	}
	if (checkForNull[3] != "Press" && checkForNull[3] != "Submitted") {
		if (!checkForNull[4]) {
			errors = errors + errorArray[4];
		}
		if (!checkForNull[5]) {
			errors = errors + errorArray[5];
		}
	}
	var checkJournal = document.getElementById("journal").value.trim();
	var checkJName = document.getElementById("jtitle").value;
	var checkJAbbrev = document.getElementById("jabbrev").value;
	if (checkJournal == "") {
		errors = errors + "<li>No journal was selected from the drop down menu.<\li>";
	}
	else if (checkJournal === "Other..." && !checkJName) {
		errors = errors + "<li>The journal was not listed in the drop down, but no Journal Name was entered.<\li>";
	}
	else if (checkJournal === "Other..." && !checkJAbbrev) {
		errors = errors + "<li>The journal was not listed in the drop down, but no Journal Abbreviation was entered.<\li>";
	}
	var checkForCheckedUsage = new Array();
	var use = document.getElementById("usage");
	var use_array = use.getElementsByTagName('input');
	for (var i = 0; i < use_array.length; i++) {
		checkForCheckedUsage[i] = use_array[i].checked;
	}
	if (checkForCheckedUsage.indexOf(true) == -1) {
		errors = errors + errorArray[7];
	}
	var checkForCheckedSE = new Array();
	var se = document.getElementById("se");
	var se_array = se.getElementsByTagName('input');
	for (var i = 0; i < se_array.length; i++) {
		checkForCheckedSE[i] = se_array[i].checked;
	}
	if (checkForCheckedSE.indexOf(true) == -1) {
		errors = errors + errorArray[8];
	}
	if (!errors) {
		return true;
	}
	else {
		errors = "<ul>" + errors + "</ul>\r\n";
		var verify = document.getElementById("submission");
		var input = "You did not fill in all form fields.  Please fix the following errors:\r" + errors;
		verify.innerHTML = input;
		verify.className = 'errors bordered';
		window.scrollTo(0,0);
		return false;
	}
	var frm = document.getElementById("myform");
	if(frm) {
		frm.action = '';
	}
	return false;
}

function addClass(elemId, cName) {
	setTimeout(function(){document.getElementById(elemId).className = cName;},350);
}