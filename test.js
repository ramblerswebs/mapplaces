/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

               var map = new L.Map('map', {center: new L.LatLng(54.221592,-3.355007), zoom: 5});
                var osm = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                var osm2= new L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
			maxZoom: 18,
			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
				'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
				'Imagery Ã‚Â© <a href="http://mapbox.com">Mapbox</a>',
			id: 'examples.map-i875mjb7'});
                var ggl = new L.Google();
                var ggl2 = new L.Google('TERRAIN');
                map.addLayer(ggl);
                map.addControl(new L.Control.Layers( { 'Google':ggl, 'Google Terrain':ggl2,'OSM':osm}, {}));
                var progress = document.getElementById('progress');
		var progressBar = document.getElementById('progress-bar');
