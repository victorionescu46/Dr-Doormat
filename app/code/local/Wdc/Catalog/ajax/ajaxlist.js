var xmlhttp;
var txtId;

function showButton(str, id) {

    txtId = id;
    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
    //var url = "/app/code/local/Wdc/Catalog/ajax/list.php";
    var url = "/ajax.php";
    url = url + "?price_id=" + str;
    xmlhttp.onreadystatechange = stateChanged;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function stateChanged() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("txtHint" + txtId).innerHTML = xmlhttp.responseText;
    }
}

function GetXmlHttpObject() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject) {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

