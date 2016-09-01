/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function getMouseMoveAction(e, map) {
    var zoom = map.getZoom();
    var p = new LatLon(e.latlng.lat, e.latlng.lng);
    var grid = OsGridRef.latLonToOsGrid(p);

    if (zoom > 16) {
        gr = grid.toString(8);
        gridref = '8 figure grid reference<br/><span class="osgridref">' + gr + "</span><br/>";
    } else {
        gr = grid.toString(6);
        gridref = '6 figure grid reference<br/><span class="osgridref">' + gr + "</span><br/>";
    }
    if (gr==""){
        gridref="Outside OS Grid<br/>";
    }

    if (zoom > 12) {
        var bounds = osGridToLatLongSquare(grid, 100);
        // change rectangle
        gridsquare100.setLatLngs(bounds);

    }
    if (zoom > 16) {
        var bounds2 = osGridToLatLongSquare(grid, 10);
        // change rectangle
        gridsquare10.setLatLngs(bounds2);
    } else {
        gridsquare10.setLatLngs([[84, -89], [84.00001, -89.000001]]);
    }
    var lng = e.latlng.lng.toFixed(5);
    var lat = e.latlng.lat.toFixed(5);
    var value = "Lat/long: " + lat + ", " + lng;
    return  gridref + value;
}

function osGridToLatLongSquare(gridref, size) {
    //  if (!(gridref instanceof OsGridRef))
    //      throw new TypeError('gridref is not OsGridRef object');

    var E1 = Math.floor(gridref.easting / size) * size;
    var N1 = Math.floor(gridref.northing / size) * size;
    var E2 = E1 + size;
    var N2 = N1 + size;
    var g1 = new OsGridRef(E1, N1);
    var g2 = new OsGridRef(E1, N2);
    var g3 = new OsGridRef(E2, N2);
    var g4 = new OsGridRef(E2, N1);
    var ll1 = OsGridRef.osGridToLatLon(g1);
    var ll2 = OsGridRef.osGridToLatLon(g2);
    var ll3 = OsGridRef.osGridToLatLon(g3);
    var ll4 = OsGridRef.osGridToLatLon(g4);

    var bounds = [[ll1.lat, ll1.lon],
        [ll2.lat, ll2.lon],
        [ll3.lat, ll3.lon],
        [ll4.lat, ll4.lon],
        [ll1.lat, ll1.lon]];
    return bounds;
}
function osMapGrid(L, layer) {
    style = {color: '#333366', weight: 1,opacity: 0.2};
    for (east = 0; east < 700500; east += 100000) {
        lines = new Array();
        i = 0;
        for (north = 0; north < 1300500; north += 10000) {
            gr = new OsGridRef(east, north);
            latlong = OsGridRef.osGridToLatLon(gr);
            lines[i] = new L.latLng(latlong.lat, latlong.lon);
            i++;
        }
       // L.polyline(lines, style).addTo(map);
       layer.addLayer(L.polyline(lines, style));
    }
    for (north = 0; north < 1300500; north += 100000) {
        lines = new Array();
        i = 0;
        for (east = 0; east < 700500; east += 10000) {
            gr = new OsGridRef(east, north);
            latlong = OsGridRef.osGridToLatLon(gr);
            lines[i] = new L.latLng(latlong.lat, latlong.lon);
            i++;
        }
       // L.polyline(lines, style).addTo(map);
       layer.addLayer(L.polyline(lines, style));
    }
}

function loadPlaceInfo($url)
{
    el = document.getElementById("placeinfo");
    el.innerHTML = "<p>Fetching descriptions/usage ...</p>";
    ajax($url, "", "placeinfo");
    modal.style.display = "block";

}

function reportDescription($gr)
{
    $url = "?option=report";
    $params = "gridref=" + $gr + "&type=description";
    ajax($url, $params, "placereport");
}
function reportGridref($gr)
{
    $url = "?option=report";
    $params = "gridref=" + $gr + "&type=gridref";
    ajax($url, $params, "placereport");
}
function processReport()
{
    $url = "?option=processReport";
    var $ids = ['Report_Text', 'Report_Type', 'Report_GR'];
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
    document.getElementById($div).innerHTML = "<div class='reportbutton-red'>Cancelled</div>";
}
function ajax($url, $params, $div)
{
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else
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


function addPlace($list, $gr, $no, $lat, $long, $icon)
{
    var marker = L.marker([$lat, $long], {icon: $icon, gridref: $gr, no: $no, lat: $lat, long: $long});
    if ($gr.length == 8) {
        $grdisp = $gr.substr(0, 2) + " " + $gr.substr(2, 3) + " " + $gr.substr(5, 3);
    } else {
        $grdisp = $gr;
    }
    marker.bindPopup("<b>Grid Ref " + $grdisp + "</b><br/>Lat/Long " + $lat + " " + $long);
    marker.on('click', onClick);
    $list.push(marker);
}
function onClick(e) {
    // console.log(this.options.gridref);
    // console.log(this.options.no);
    loadPlaceInfo("index.php?option=details&gr=" + this.options.gridref + "&no=" + this.options.no + "&lat=" + this.options.lat + "&long=" + this.options.long);
}

function photos($gr) {
    page = "http://www.geograph.org.uk/gridref/" + $gr;
    window2 = open(page, "photos", "scrollbars=yes,width=990,height=480,menubar=yes,resizable=yes,status=yes");
}
function streetmap($gr) {
    page = "http://www.streetmap.co.uk/grid/" + $gr + "&z=115";
    window2 = open(page, "streetmap", "scrollbars=yes,width=900,height=580,menubar=yes,resizable=yes,status=yes");
}
function googlemap($lat, $long) {
    // https://www.google.com/maps/place/40.7028722+-73.9868281/@40.7028722,-73.9868281,15z
    page = "https://www.google.com/maps/place/" + $lat + "+" + $long + "/@" + $lat + "," + $long + ",15z";
    window2 = open(page, "Google Streetview", "scrollbars=yes,width=900,height=580,menubar=yes,resizable=yes,status=yes");
}



