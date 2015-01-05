<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of display
 *
 * @author Chris Vaughan
 */
class PlacesDisplay {

    private $db;

    function __construct($database) {
        $this->db = $database;
    }

    function display() {
        echo "<!doctype html>
              <html xml:lang=\"en-gb\" lang=\"en-gb\" >";
        echo "<head>\r\n";
        echo "<link rel=\"stylesheet\" href=\"http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css\" />\r\n";
        echo "<script src=\"http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js\"></script>\r\n";
        echo "<link rel=\"stylesheet\" href=\"dist/ramblerplaces.css\" />\r\n";
        echo "<script src=\"dist/ramblersplaces.js\"></script>\r\n";
        // google options
        echo "<script src=\"http://maps.google.com/maps/api/js?v=3&sensor=false\"></script>\r\n";
        echo "<script src=\"dist/Google.js\"></script>\r\n";
        // clustering
        echo "<link rel=\"stylesheet\" href=\"dist/MarkerCluster.css\" />\r\n";
        echo "<link rel=\"stylesheet\" href=\"dist/MarkerCluster.Default.css\" />\r\n";
        echo "<link rel=\"stylesheet\" href=\"dist/screen.css\" />\r\n";
        echo "<script src=\"dist/leaflet.markercluster-src.js\"></script>\r\n";
// search
        echo "<script src=\"dist/l.control.geosearch.js\"></script>
        <script src=\"dist/l.geosearch.provider.openstreetmap.js\"></script>
        <link rel=\"stylesheet\" href=\"dist/l.geosearch.css\" />";
        echo "<style type=\"text/css\">
           #map { height: 400px; }
           </style>";
        echo "</head>\r\n";
        echo "<body>\r\n";
        echo "<div id=\"progress\"><div id=\"progress-bar\"></div></div>\r\n";
        echo "<div id=\"map\"></div>\r\n";
        echo "<script>
                var map = new L.Map('map', {center: new L.LatLng(54.221592,-3.355007), zoom: 5});
                var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 18,
			attribution: 'Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> '});
                var osm2= new L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
			maxZoom: 18,
			attribution: 'Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors, ' +
				'<a href=\"http://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, ' +
				'Imagery Â© <a href=\"http://mapbox.com\">Mapbox</a>',
			id: 'examples.map-i875mjb7'});
                L.control.scale().addTo(map);
                var ggl = new L.Google('ROADMAP');
                var ggl2 = new L.Google('HYBRID');
                var ggl3 = new L.Google('SATELLITE');
                map.addLayer(osm);
                 new L.Control.GeoSearch({
                provider: new L.GeoSearch.Provider.OpenStreetMap(),
                position: 'topright',
                showMarker: false
                }).addTo(map); 
                map.addControl(new L.Control.Layers( {'OSM':osm,'OSM Terrain':osm2, 'Google':ggl, 'Google Satellite':ggl3, 'Google Hybrid':ggl2}, {}));
 ";
        echo "var progress = document.getElementById('progress');
		var progressBar = document.getElementById('progress-bar');

		function updateProgressBar(processed, total, elapsed, layersArray) {
			if (elapsed > 1000) {
				// if it takes more than a second to load, display the progress bar:
				progress.style.display = 'block';
				progressBar.style.width = Math.round(processed/total*100) + '%';
			}

			if (processed === total) {
				// all markers processed - hide the progress bar:
				progress.style.display = 'none';
			}
		}

		var markers = L.markerClusterGroup({ chunkedLoading: true, chunkProgress: updateProgressBar });

		var markerList = [];";
        $this->db->getPlaces();
        echo "</script>";
        echo "<div class=\"scroll\" id=\"placeinfo\"></div>\r\n";
        echo "</body>";
        echo "</html>";
    }

}
