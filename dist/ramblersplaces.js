/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function loadPlaceInfo($url)
{
    ajax($url, "", "placeinfo");
}


function placeReport($gr)
{
    $url = "?option=report";
    $params = "gridref=" + $gr;
    ajax($url, $params, "placereport");
}
function processReport()
{
    $url = "?option=processReport";
    var $ids = ['Report_Text', 'Report_New', 'Report_Location'];
    $params = createParams($ids);
    ajax($url, $params, "placereport");
}
function createParams($array)
{
    for (var i = 0; i < $array.length; i++) {
        $name = $array[i];
        el = document.getElementById($name);
        if (el.type) {
            switch (el.type) {
                case 'checkbox':
                    $array[i] = $array[i] + "=" + document.getElementById($name).checked;
                    break;
                case 'radio':
                    $array[i] = $array[i] + "=" + document.getElementById($name).checked;
                    break;
                default:
                    $array[i] = $array[i] + "=" + document.getElementById($name).value;
            }
        }
    }
    return $array.join("&");
}
function cancelReport()
{
    $div = "placereport";
    document.getElementById($div).innerHTML = "Cancelled";
}
function ajax($url, $params, $div)
{
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function ()
    {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
        {
            document.getElementById($div).innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("POST", $url, true);
    //Send the proper header information along with the request
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.setRequestHeader("Content-length", $params.length);
    xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send($params);
}


function addPlace($list, $gr, $no, $lat, $long)
{
    var marker = L.marker([$lat, $long], {gridref: $gr});
    marker.bindPopup("<b>" + $gr + "</b> (" + $no + " )</a>");
    marker.on('click', onClick);
    $list.push(marker);
}
function onClick(e) {
    // console.log(this.options);
    // window.open(this.options);
    console.log(this.options.gridref);
    loadPlaceInfo("index.php?option=details&id=" + this.options.gridref);
}

function photos($gr) {
    page = "http://www.geograph.org.uk/gridref/" + $gr;
    window2 = open(page, "photos", "scrollbars=yes,width=600,height=600,menubar=yes,resizable=yes,status=yes");
}
