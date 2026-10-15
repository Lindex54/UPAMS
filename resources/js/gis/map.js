import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const DEFAULT_CENTER = [1.05, 33.85];
const DEFAULT_ZOOM = 8;
const FOCUS_ZOOM = 16;

const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

/**
 * Build the summary card shown on hover (tooltip) and on click (popup).
 */
const cardHtml = (feature, layer, { withLink = false } = {}) => {
    const rows = feature.details
        .map(([label, value]) => `<div class="gis-card__row"><dt>${escapeHtml(label)}</dt><dd>${escapeHtml(value)}</dd></div>`)
        .join('');
    const link = withLink && feature.url
        ? `<a class="gis-card__link" href="${escapeHtml(feature.url)}">Open record →</a>`
        : '';

    return `
        <div class="gis-card" style="--gis-color: ${escapeHtml(layer.color)}">
            <div class="gis-card__head">
                <span class="gis-card__layer">${escapeHtml(layer.label.replace(/s$/, ''))}</span>
                ${feature.status ? `<span class="gis-card__status">${escapeHtml(feature.status)}</span>` : ''}
            </div>
            <p class="gis-card__title">${escapeHtml(feature.title)}</p>
            ${feature.subtitle ? `<p class="gis-card__subtitle">${escapeHtml(feature.subtitle)}</p>` : ''}
            ${rows ? `<dl class="gis-card__rows">${rows}</dl>` : ''}
            <p class="gis-card__coords">${feature.latitude.toFixed(5)}, ${feature.longitude.toFixed(5)}</p>
            ${link}
        </div>`;
};

const pinIcon = (feature, layer) => {
    const isCampus = feature.layer === 'campuses';
    const label = isCampus ? escapeHtml(feature.badge ?? '') : '';

    return L.divIcon({
        className: 'gis-pin-wrap',
        html: `<span class="gis-pin ${isCampus ? 'gis-pin--labelled' : 'gis-pin--dot'}" style="--gis-color: ${escapeHtml(layer.color)}">${label}</span>`,
        iconSize: isCampus ? [52, 26] : [16, 16],
        iconAnchor: isCampus ? [26, 13] : [8, 8],
    });
};

/**
 * Create the Leaflet map and return a small controller the Alpine panel drives.
 *
 * @param {HTMLElement} element
 * @param {{ features: Array<object>, layers: Record<string, {label: string, color: string}>, onSelect: (id: string) => void }} options
 */
export function createGisMap(element, { features, layers, onSelect }) {
    const baseLayers = {
        streets: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }),
        satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics',
        }),
    };

    const map = L.map(element, { zoomControl: false, layers: [baseLayers.streets] }).setView(DEFAULT_CENTER, DEFAULT_ZOOM);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.control.scale({ position: 'bottomleft', imperial: false }).addTo(map);

    const markers = new Map();
    const group = L.featureGroup().addTo(map);

    features.forEach((feature) => {
        const layer = layers[feature.layer];
        const marker = L.marker([feature.latitude, feature.longitude], {
            icon: pinIcon(feature, layer),
            title: feature.title,
            riseOnHover: true,
            zIndexOffset: feature.layer === 'campuses' ? 1000 : 0,
        });

        marker.bindTooltip(cardHtml(feature, layer), { direction: 'top', offset: [0, -14], className: 'gis-tooltip', opacity: 1 });
        marker.bindPopup(cardHtml(feature, layer, { withLink: true }), { className: 'gis-popup', closeButton: true, autoPanPadding: [40, 40] });
        marker.on('click', () => {
            marker.closeTooltip();
            map.flyTo(marker.getLatLng(), Math.max(map.getZoom(), FOCUS_ZOOM), { duration: 0.8 });
            onSelect(feature.id);
        });

        markers.set(feature.id, marker);
        group.addLayer(marker);
    });

    const fitVisible = () => {
        const bounds = group.getBounds();

        if (bounds.isValid()) {
            map.flyToBounds(bounds.pad(0.25), { maxZoom: 13, duration: 0.8 });
        } else {
            map.flyTo(DEFAULT_CENTER, DEFAULT_ZOOM, { duration: 0.8 });
        }
    };

    if (group.getLayers().length > 0) {
        map.fitBounds(group.getBounds().pad(0.25), { maxZoom: 13 });
    }

    return {
        show(visibleIds) {
            markers.forEach((marker, id) => {
                const shouldShow = visibleIds.has(id);

                if (shouldShow && !group.hasLayer(marker)) {
                    group.addLayer(marker);
                } else if (!shouldShow && group.hasLayer(marker)) {
                    group.removeLayer(marker);
                }
            });
        },
        focus(id) {
            const marker = markers.get(id);

            if (!marker) {
                return;
            }

            map.flyTo(marker.getLatLng(), FOCUS_ZOOM, { duration: 0.8 });
            map.once('moveend', () => marker.openPopup());
        },
        preview(id) {
            markers.get(id)?.openTooltip();
        },
        clearPreview(id) {
            markers.get(id)?.closeTooltip();
        },
        fitVisible,
        setBaseLayer(name) {
            Object.entries(baseLayers).forEach(([key, tileLayer]) => {
                if (key === name) {
                    tileLayer.addTo(map);
                } else {
                    map.removeLayer(tileLayer);
                }
            });
        },
        invalidateSize() {
            map.invalidateSize();
        },
    };
}
