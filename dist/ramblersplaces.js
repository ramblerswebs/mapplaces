/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function loadPlaceInfo($url)
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
            document.getElementById("placeinfo").innerHTML = xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET", $url, true);
    xmlhttp.send();
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

 function photos(gr) {
   page="http://www.geograph.org.uk/gridref/"+gr; 
   window2=open(page,"photos","scrollbars=yes,width=600,height=600,menubar=yes,resizable=yes,status=yes");
   }
