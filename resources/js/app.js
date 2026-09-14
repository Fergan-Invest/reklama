import './bootstrap';

// Advertising-object form: one map point and automatically calculated area.
document.addEventListener('DOMContentLoaded', () => {
    const advertisingType = document.getElementById('advertising_type');
    const advertisingDescription = document.getElementById('advertising-type-description');
    const syncAdvertisingDescription = () => {
        if (!advertisingType || !advertisingDescription) return;
        advertisingDescription.textContent = advertisingType.selectedOptions[0]?.dataset.description || '';
    };
    advertisingType?.addEventListener('change', syncAdvertisingDescription);
    syncAdvertisingDescription();

    const passportRadios = [...document.querySelectorAll('input[name="has_passport"]')];
    const passportWrap = document.getElementById('passport-details-wrap');
    const passportDetails = passportWrap?.querySelector('textarea');
    const syncPassport = () => {
        const hasPassport = passportRadios.find(radio => radio.checked)?.value === '1';
        passportWrap?.classList.toggle('hidden', !hasPassport);
        if (passportDetails) {
            passportDetails.required = hasPassport;
            if (!hasPassport) passportDetails.value = '';
        }
    };
    passportRadios.forEach(radio => radio.addEventListener('change', syncPassport));
    syncPassport();

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
    const locationStatus = document.getElementById('location-status');
    const initialLat = Number(element.dataset.lat) || 40.3777;
    const initialLng = Number(element.dataset.lng) || 71.7978;
    const hasLocation = Boolean(element.dataset.lat && element.dataset.lng);
    const map = L.map(element).setView([initialLat, initialLng], hasLocation ? 17 : 13);
    element._leafletMap = map;
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 20, attribution: '&copy; OpenStreetMap' }).addTo(map);
    let marker = hasLocation ? L.marker([initialLat, initialLng], { draggable: true }).addTo(map) : null;
    const save = ({ lat, lng }) => {
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        if (locationStatus) {
            locationStatus.textContent = 'Joylashuv belgilandi.';
            locationStatus.classList.remove('field-error');
        }
    };
    const place = (latlng) => {
        if (!marker) {
            marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', e => save(e.target.getLatLng()));
        } else marker.setLatLng(latlng);
        save(latlng);
    };
    marker?.on('dragend', e => save(e.target.getLatLng()));
    map.on('click', e => place(e.latlng));

    document.querySelector('[data-step-target="4"]')?.addEventListener('click', () => {
        window.setTimeout(() => map.invalidateSize(true), 80);
    });

    const locateButton = document.getElementById('locate-single-position');
    locateButton?.addEventListener('click', () => {
        if (!navigator.geolocation) {
            if (locationStatus) locationStatus.textContent = 'Браузер геолокацияни қўллаб-қувватламайди.';
            return;
        }
        locateButton.disabled = true;
        if (locationStatus) locationStatus.textContent = 'Жойлашув аниқланмоқда…';
        navigator.geolocation.getCurrentPosition(({ coords }) => {
            const point = L.latLng(coords.latitude, coords.longitude);
            place(point);
            map.setView(point, 18);
            map.invalidateSize(true);
            locateButton.disabled = false;
            if (locationStatus) locationStatus.textContent = 'Жойлашув белгиланди.';
        }, () => {
            locateButton.disabled = false;
            if (locationStatus) locationStatus.textContent = 'Жойлашувни аниқлаб бўлмади. Харитани босиб белгиланг.';
        }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 30000 });
    });
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
