L.Control.MousePosition = L.Control.extend({
    options: {
        position: 'bottomleft',
        separator: ', ',
        emptyString: 'Unavailable',
        lngFirst: false,
        numDigits: 5,
        lngFormatter: undefined,
        latFormatter: undefined,
        prefix: "Lat/Long: "
    },
    onAdd: function (map) {
        this._container = L.DomUtil.create('div', 'leaflet-control-mouseposition');
        L.DomEvent.disableClickPropagation(this._container);
        map.on('mousemove', this._onMouseMove, this);
        this._container.innerHTML = this.options.emptyString;
        return this._container;
    },
    onRemove: function (map) {
        map.off('mousemove', this._onMouseMove)
    },
    _onMouseMove: function (e) {
        // var LatLon = require('geodesy').LatLonSpherical;
        // get Lat/Long and Grid Reference
        var p = new LatLon(e.latlng.lat, e.latlng.lng);
        var grid = OsGridRef.latLonToOsGrid(p);
        var gr = grid.toString(6);
        var zoom = map.getZoom();
        if (zoom > 12) {
            var rect = OsGridRef.osGridToLatLongSquare(grid);
            var bounds = [[rect[0].lat, rect[0].lng],
                [rect[1].lat, rect[0].lng],
                [rect[1].lat, rect[1].lng],
                [rect[0].lat, rect[1].lng],
                [rect[0].lat, rect[0].lng]];
            // change rectangle
            gridsquare.setLatLngs(bounds);
        }
        var lng = this.options.lngFormatter ? this.options.lngFormatter(e.latlng.lng) : L.Util.formatNum(e.latlng.lng, this.options.numDigits);
        var lat = this.options.latFormatter ? this.options.latFormatter(e.latlng.lat) : L.Util.formatNum(e.latlng.lat, this.options.numDigits);
        var value = this.options.lngFirst ? lng + this.options.separator + lat : lat + this.options.separator + lng;
        var prefixAndValue = 'OS Grid Ref: <span class="osgridref">' + gr +"</span><br/>"+ this.options.prefix + ' ' + value;
        this._container.innerHTML = prefixAndValue;
    }

});

L.Map.mergeOptions({
    positionControl: false
});

L.Map.addInitHook(function () {
    if (this.options.positionControl) {
        this.positionControl = new L.Control.MousePosition();
        this.addControl(this.positionControl);
    }
});

L.control.mousePosition = function (options) {
    return new L.Control.MousePosition(options);
};
