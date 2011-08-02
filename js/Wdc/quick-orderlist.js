var xmlhttp;
var txtId=0;

function skuLookup(str) {

    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
    // var url = "/app/code/local/Wdc/QuickOrder/Ajax/skulookup.php";
    var url = "/Wdc/qol/skulup.php";
    url = url + "?sku=" + str;
    xmlhttp.onreadystatechange = stateChangedSku;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function stateChangedSku() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("skuid").innerHTML = xmlhttp.responseText;
    }
}

function getList(qty, eid) {

    txtId = eid;
    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var url = "Wdc/qol/Block/qoAddtoList.php";
    url = url + "?qty=" + qty + "&eid=" + eid;
    xmlhttp.onreadystatechange = stateChangedList;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}


function stateChangedList() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("list").innerHTML = xmlhttp.responseText;
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

