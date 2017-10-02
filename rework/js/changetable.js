// Function to remove a specific row from a table
function removeRow(obj, row){
	var j = document.getElementById(obj);
	var rws = j.getElementsByTagName('TBODY')[0].getElementsByTagName('TR');
	var size = rws.length;
	if (row == -9999) {
		row = size - 1;
	}
	var removeMe = rws[row];
	if (size > 1) {
		removeMe.parentNode.removeChild(removeMe);
	}
	if (size == 2) {
		var k = j.parentNode.getElementsByTagName('INPUT')[1];
		k.disabled = true;
	}
}

// Function to remove a row in the middle of a table with numbered entries before and after the deletion
function removeRowInline(obj, row) {
	row--;
	removeRow(obj, row);
	var tbl = document.getElementById(obj).getElementsByTagName('tbody')[0];
	var trows = tbl.getElementsByTagName('tr');
	changeRowsInline(trows, row, obj, -1);
}

// Gets the total number of cells in a table
function totalNumber(itemarray) {
	var sizeme, rows, root, itemI;
	sizeme = 0;
	for (i=0; i < itemarray.length; i++) {
		root = document.getElementById(itemarray[i]).getElementsByTagName('TBODY')[0];
		rows = root.getElementsByTagName('TR');
		sizeme = sizeme + rows.length;
	}
	return sizeme;
}

// Add a row to a table. Will check for and add any inputs and selection boxes
function addRow(r) {
	var allRows, cClk, cInp, cRow, cSel, idName, idNo, i, j, myRoot, newArray, newId, newNo, newOnclk, size, sizeMe, thisClick;
	myRoot = document.getElementById(r).getElementsByTagName('TBODY')[0];
	allRows = myRoot.getElementsByTagName('TR');
	sizeMe = r.length;
	size = allRows.length;
	idNo = parseInt(allRows[0].id.substr(sizeMe), 10) + 1;
	newId = generateUniqueIdName(r, idNo);
	newNo = newId.substr(sizeMe);
	cRow = allRows[0].cloneNode(true);
	replaceIdNumber(cRow, addZero(1), addZero(size + 1));
	myRoot.appendChild(cRow);
	var j = document.getElementById(r).parentNode.getElementsByTagName('INPUT')[1];
	j.disabled = false;
}

// Checks the document to see if the ID name is present. If so, adds 1 to the id number and recurses.
// The returned value is the last generated number in the recursion
function generateUniqueIdName(idname, idnumber) {
	insertid = idname + addZero(idnumber);
	if (document.getElementById(insertid)) {
		idnumber = idnumber + 1;
		insertid = generateUniqueIdName(idname, idnumber);
	}
	return insertid;
}

// Creates an incremented name as a text field
function generateTextName(oldname, idnumber) {
	insertid = oldname + " " + addZero(idnumber);
	return insertid
}

// If an integer is less than ten, a zero is tacked onto the beginning and is converted to a string
function addZero (idnumber) {
	var insertid;
	idnumber = idnumber.toString()
	if (idnumber < 10) {
		insertid = "0" + idnumber;
	}
	else {
		insertid = idnumber;
	}
	return insertid;
}

// Finds the number of rows in a table
function findNumberOfRows(tableId, storeId) {
	var table = document.getElementById(tableId);
	var rows = table.getElementsByTagName('tr');
	var storeHere = document.getElementById(storeId);
	storeHere.value = rows.length;
}

// Changes the value of an element
function changeValue(itemId, newValue) {
	var elem = document.getElementById(itemId);
	elem.value = newValue;
}

// Adds a row anywhere in the table. Auto changes any subsequent row if there are numerics associated
function addRowInline(tblId, rowId) {
	var rowIndex = rowId.replace(/[a-z]/gi,'');
	rowIndex = rowIndex.replace(/^[0]+/g,'');
	var tbl = document.getElementById(tblId).getElementsByTagName('tbody')[0];
	var row = document.getElementById(rowId);
	var trows = tbl.getElementsByTagName('tr');
	if (trows.length == 1) {
		var k = row.getElementsByTagName('INPUT')[3];
		k.disabled = false;
	}
	
	var cRow = row.cloneNode(true);
	var newIndex = parseInt(rowIndex) + 1;
	replaceIdNumber(cRow, rowIndex, newIndex);
	
	var addBeforeMe = trows[rowIndex];
	tbl.insertBefore(cRow, addBeforeMe);
	
	changeRowsInline(trows, rowIndex, tblId);
}

// Changes the inline rows when a table is changed
function changeRowsInline(trows, rowIndex, tblId, inc=1) {
	for (var i = rowIndex; i < trows.length; ++i) {
		var thisRow = trows[i];
		replaceIdNumber(thisRow, addZero(i), addZero(i + inc));
		if (thisRow.getAttribute("value") != i.toString()) {
			thisRow.setAttribute("value", "");
		}
	}
	var input = document.getElementById("no_" + tblId);
	input.setAttribute("value", trows.length);
}

function replAll(findme, replaceme, str) {
	return str.replace(new RegExp(escapeRegExp(findme), 'g'), replaceme);
}

function escapeRegExp(string) {
	string = String(string)
	return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}

function replaceIdNumber(node, oldId, newId) {
	node.innerHTML = replAll(oldId, newId, node.innerHTML.toString());
	node.id = node.id.replace(oldId, newId);
}