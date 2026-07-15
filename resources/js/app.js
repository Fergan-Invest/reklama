import './bootstrap';

// Advertising-object form: one map point and automatically calculated area.
document.addEventListener('DOMContentLoaded', () => {
    const length = document.getElementById('area_length');
    const width = document.getElementById('area_width');
    const area = document.getElementById('total_area');
    const calculateArea = () => {
        if (!length || !width || !area) return;
        const result = Number(length.value) * Number(width.value);
        area.value = Number.isFinite(result) && result > 0 ? result.toFixed(2) : '';
    };
    length?.addEventListener('input', calculateArea);
    width?.addEventListener('input', calculateArea);

    const element = document.getElementById('location-map');
    if (!element || typeof L === 'undefined') return;
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const initialLat = Number(element.dataset.lat) || 40.3777;
    const initialLng = Number(element.dataset.lng) || 71.7978;
    const hasLocation = Boolean(element.dataset.lat && element.dataset.lng);
    const map = L.map(element).setView([initialLat, initialLng], hasLocation ? 17 : 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 20, attribution: '&copy; OpenStreetMap' }).addTo(map);
    let marker = hasLocation ? L.marker([initialLat, initialLng], { draggable: true }).addTo(map) : null;
    const save = ({ lat, lng }) => { latInput.value = lat.toFixed(7); lngInput.value = lng.toFixed(7); };
    const place = (latlng) => {
        if (!marker) {
            marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', e => save(e.target.getLatLng()));
        } else marker.setLatLng(latlng);
        save(latlng);
    };
    marker?.on('dragend', e => save(e.target.getLatLng()));
    map.on('click', e => place(e.latlng));
});

document.addEventListener('DOMContentLoaded', () => {
    const element = document.getElementById('show-map');
    if (!element || !element.dataset.point || typeof L === 'undefined') return;
    const point = JSON.parse(element.dataset.point);
    if (!Array.isArray(point) || !point[0] || !point[1]) return;
    const map = L.map(element).setView(point, 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 20, attribution: '&copy; OpenStreetMap' }).addTo(map);
    L.marker(point).addTo(map);
});
