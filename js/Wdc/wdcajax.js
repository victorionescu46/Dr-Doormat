//var xmlhttp;
var xmlhttp2;
var txtId;
var proId;
var blid;

function toggleVisibility(bt, theObject) {
    if (bt) {
        theObject.style.visibility = 'visible';
    }
    else {
        theObject.style.visibility = 'hidden';
    }
}

function blockPop(bid, bt, theObject) {

    toggleVisibility(bt, theObject)
    blid = bid;
    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var url = "/Wdc/popup/blockPop.php";
    url = url + "?bid=" + bid;
    xmlhttp.onreadystatechange = stateChangedblockPop;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function showButton(ptype, str, id) {

    txtId = id;
    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
   
    var url = "/Wdc/options/drops.php";
    url = url + "?price_id=" + str;
    xmlhttp.onreadystatechange = stateChangedBut;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);

    xmlhttp2 = GetXmlHttpObjectDuex();

    if (xmlhttp2 == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
   
    var url2 = "/Wdc/options/productName.php";
    url2 = url2 + "?price_id=" + str + "&ptype=" + ptype;
    xmlhttp2.onreadystatechange = stateChangedProLab;
    xmlhttp2.open("GET", url2, true);
    xmlhttp2.send(null);
}

function stateChangedProLab() {
    if (xmlhttp2.readyState == 4) {
        document.getElementById("productLabel" + txtId).innerHTML = xmlhttp2.responseText;
    }
}

function stateChangedBut() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("txtHint" + txtId).innerHTML = xmlhttp.responseText;
    }
}



function deleteRow(lid) {

    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
    var url = "/Wdc/qol/deleterow.php";
    url = url + "?lid=" + lid;
    xmlhttp.onreadystatechange = stateChangedDelete;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function stateChangedDelete() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("list").innerHTML = xmlhttp.responseText;
    }
}

function skuLookup(str) {

    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
   
    var url = "/Wdc/qol/skulup.php";
    url = url + "?sku=" + str;
    xmlhttp.onreadystatechange = stateChangedSku;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);

    xmlhttp2 = GetXmlHttpObject();
    var url2 = "/Wdc/qol/sidesku.php";
    url2 = url2 + "?sku=" + str;
    xmlhttp2.onreadystatechange = stateChangedSkuList;
    xmlhttp2.open("GET", url2, true);
    xmlhttp2.send(null);
    
    
}

function stateChangedSku() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("skuid").innerHTML = xmlhttp.responseText;
    }
}

function stateChangedSkuList() {
    if (xmlhttp2.readyState == 4) {
        document.getElementById("skulike").innerHTML = xmlhttp2.responseText;
    }
}

function getList(qty, eid) {

    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var url = "/Wdc/qol/qoAddtoList.php";
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

function addCart(sid) {

    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }

    var url = "/Wdc/qol/addCart.php";
    url = url + "?sid=" + sid;
    //xmlhttp.onreadystatechange = stateChangedCart;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}


function stateChangedCart() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("list").innerHTML = xmlhttp.responseText;
    }
}

function updategrain(itemNo, checkVar, RadioCheck) {

    proId = itemNo;
    xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
    var url  = "/Wdc/cart/updateCrush.php";
    
    url = url + "?check=" + checkVar + "&itemLineNumber=" + itemNo + "&RadioPage=" + RadioCheck;
    
    xmlhttp.onreadystatechange = stateChangedCrush;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
}

function stateChangedCrush() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("crush" + proId).innerHTML = xmlhttp.responseText;
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

function GetXmlHttpObjectDuex() {
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

function ClearBox() {
    document.getElementById('sku').value = ''

    var d = document.getElementById('skuid')
    if (!d.style.display == "none") {
        d.style.display == "visible";
    }
}


function stateChangedblockPop() {
    if (xmlhttp.readyState == 4) {
        document.getElementById("block" + blid).innerHTML = xmlhttp.responseText;
    }
}



//function callAJAX(url, pageElement, callMessage) {
//    document.getElementById(pageElement).innerHTML = callMessage;
//    try {
//        req = new XMLHttpRequest(); /* e.g. Firefox */
//    } catch (e) {
//        try {
//            req = new ActiveXObject("Msxml2.XMLHTTP");
//            /* some versions IE */
//        } catch (e) {
//            try {
//                req = new ActiveXObject("Microsoft.XMLHTTP");
//                /* some versions IE */
//            } catch (E) {
//                req = false;
//            }
//        }
//    }

//    req.onreadystatechange = function() { responseAJAX(pageElement); };
//    req.open("GET", url, true);
//    req.send(null);

//}

//function responseAJAX(pageElement) {
//    var output = '';
//    if (req.readyState == 4) {
//        if (req.status == 200) {
//            output = req.responseText;
//            document.getElementById(pageElement).innerHTML = output;
//        }
//    }
//}