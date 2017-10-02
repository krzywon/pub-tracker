var XMLHttpFactories = [
	function () {return new XMLHttpRequest()},
	function () {return new ActiveXObject("Msxml2.XMLHTTP")},
	function () {return new ActiveXObject("Msxml3.XMLHTTP")},
	function () {return new ActiveXObject("Microsoft.XMLHTTP")}
];

function createXMLHTTPObject() {
	var xmlhttp = false;
	if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
	return xmlhttp;
}

function applyContent(xmlhttp, docId) {
	if (xmlhttp.readyState==4 && xmlhttp.status==200) {
  	document.getElementById(docId).innerHTML=xmlhttp.responseText;
  }
}

function showResult(obj, str) {
	var tr = obj.parentNode.parentNode;
	var oldid = tr.getAttribute('id');
	var newid = oldid + "list";
  if (str.length == 0) {
    document.getElementById(newid).innerHTML="";
    document.getElementById(newid).style.border="0px";
    return;
  }
  xmlhttp = createXMLHTTPObject;
	applyContent(xmlhttp, newId);
	/*
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById(selectId).innerHTML=xmlhttp.responseText;
    }
  }
  */
  document.getElementById(newid).style.border="1px solid #A5ACB2";
  xmlhttp.open("GET","livesearch.php?q="+str+"&id="+oldid,true);
  xmlhttp.send();
}

function fillbox(id, str) {
	var input = id.getElementsByTagName("input");
	input[0].value = str;
	var div = id.getElementsByTagName("div");
	div[0].innerHTML = ""; 
}

function getPubsByYear(year, selectId) {
	xmlhttp = createXMLHTTPObject;
	applyContent(xmlhttp, selectId);
	/*
	if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById(selectId).innerHTML=xmlhttp.responseText;
    }
  }
  */
  xmlhttp.open("GET","livepubsearch.php?year="+year,true);
  xmlhttp.send();
}

function fillModificationForm(pubTitle, selectId) {
	if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById(selectId).innerHTML=xmlhttp.responseText;

    }
  }
  xmlhttp.open("GET","livepubsearchbytitle.php?title="+pubTitle,true);
  xmlhttp.send();
}