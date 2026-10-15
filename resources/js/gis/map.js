import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const DEFAULT_CENTER = [1.05, 33.85];
const DEFAULT_ZOOM = 8;
const FOCUS_ZOOM = 16;
const BASE_LAYER_SWAP_TIMEOUT = 4000;
const HOVER_CARD_GAP = 14;
const VIEWPORT_MARGIN = 8;

const LAYER_ICONS = {
    campuses: '<path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6" />',
    assets: '<path d="M4 7.5 12 3l8 4.5M5 9v9l7 3 7-3V9M12 12l8-4.5M12 12 4 7.5M12 12v9" />',
};

const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

/**
 * Build a shadcn-style summary card, used for the hover preview and the click popup.
 */
const cardHtml = (feature, layer, { withActions = false } = {}) => {
    const icon = LAYER_ICONS[feature.layer] ?? LAYER_ICONS.assets;
    const stats = (feature.stats ?? [])
        .map(([label, value]) => `<div class="gis-card__stat"><span>${escapeHtml(value)}</span><small>${escapeHtml(label)}</small></div>`)
        .join('');
    const details = (feature.details ?? [])
        .map(([label, value]) => `<div><dt>${escapeHtml(label)}</dt><dd>${escapeHtml(value)}</dd></div>`)
        .join('');
    const actions = withActions
        ? `<div class="gis-card__footer">
                <a class="gis-card__button gis-card__button--outline" href="${escapeHtml(feature.directions_url)}" target="_blank" rel="noopener">Directions</a>
                ${feature.url ? `<a class="gis-card__button" href="${escapeHtml(feature.url)}">Open record</a>` : ''}
            </div>`
        : '<p class="gis-card__hint">Click the pin to zoom in</p>';

    return `
        <article class="gis-card" style="--gis-color: ${escapeHtml(layer.color)}">
            <header class="gis-card__header">
                <span class="gis-card__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">${icon}</svg></span>
                <div class="gis-card__heading">
                    <p class="gis-card__title">${escapeHtml(feature.title)}</p>
                    ${feature.subtitle ? `<p class="gis-card__description">${escapeHtml(feature.subtitle)}</p>` : ''}
                </div>
            </header>
            <div class="gis-card__badges">
                <span class="gis-badge gis-badge--layer">${escapeHtml(layer.label.replace(/s$/, ''))}</span>
                ${feature.badge ? `<span class="gis-badge gis-badge--mono">${escapeHtml(feature.badge)}</span>` : ''}
                ${feature.status ? `<span class="gis-badge ${feature.status === 'Active' ? 'gis-badge--success' : ''}">${escapeHtml(feature.status)}</span>` : ''}
            </div>
            ${stats ? `<div class="gis-card__stats">${stats}</div>` : ''}
            ${details ? `<dl class="gis-card__details">${details}</dl>` : ''}
            <p class="gis-card__coords">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 21s-7-5.5-7-11a7 7 0 1 1 14 0c0 5.5-7 11-7 11Z" /><circle cx="12" cy="10" r="2.5" /></svg>
                ${feature.latitude.toFixed(5)}, ${feature.longitude.toFixed(5)}
            </p>
            ${actions}
        </article>`;
};

const pinIcon = (feature, layer) => {
    const isCampus = feature.layer === 'campuses';

    return L.divIcon({
        className: 'gis-pin-wrap',
        html: `<span class="gis-pin ${isCampus ? 'gis-pin--labelled' : 'gis-pin--dot'}" style="--gis-color: ${escapeHtml(layer.color)}">${isCampus ? escapeHtml(feature.badge ?? '') : ''}</span>`,
        iconSize: isCampus ? [52, 26] : [16, 16],
        iconAnchor: isCampus ? [26, 13] : [8, 8],
    });
};

const createBaseLayers = () => ({
    streets: [
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }),
    ],
    satellite: [
        // Rural imagery is sparse beyond zoom 17, so deeper zooms upscale the last available tiles instead of going blank.
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            maxNativeZoom: 17,
            attribution: 'Imagery &copy; Esri, Maxar, Earthstar Geographics',
        }),
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            maxNativeZoom: 17,
        }),
    ],
});

/**
 * A shadcn-style hover card rendered on <body>, so the map's clipped edges can never hide it.
 * It opens above the pin when there is room, otherwise below, and stays inside the viewport.
 */
const createHoverCard = () => {
    const card = document.createElement('div');
    card.className = 'gis-hovercard';
    card.setAttribute('role', 'tooltip');
    card.hidden = true;
    document.body.appendChild(card);

    return {
        show(html, anchor) {
            card.innerHTML = html;
            card.hidden = false;
            card.dataset.state = 'open';

            const { offsetWidth: width, offsetHeight: height } = card;
            const fitsAbove = anchor.y - HOVER_CARD_GAP - height >= VIEWPORT_MARGIN;
            const fitsBelow = anchor.y + HOVER_CARD_GAP + height <= window.innerHeight - VIEWPORT_MARGIN;
            const top = fitsAbove || !fitsBelow
                ? Math.max(VIEWPORT_MARGIN, anchor.y - HOVER_CARD_GAP - height)
                : anchor.y + HOVER_CARD_GAP;
            const left = Math.min(
                Math.max(VIEWPORT_MARGIN, anchor.x - width / 2),
                window.innerWidth - width - VIEWPORT_MARGIN,
            );

            card.dataset.side = fitsAbove || !fitsBelow ? 'top' : 'bottom';
            card.style.top = `${Math.round(top)}px`;
            card.style.left = `${Math.round(left)}px`;
        },
        hide() {
            card.hidden = true;
            card.dataset.state = 'closed';
        },
    };
};

/**
 * Create the Leaflet map and return a small controller the Alpine panel drives.
 *
 * @param {HTMLElement} element
 * @param {{ features: Array<object>, layers: Record<string, {label: string, color: string}>, baseLayer: string, onSelect: (id: string) => void, onBaseLayerLoading: (isLoading: boolean) => void }} options
 */
export function createGisMap(element, { features, layers, baseLayer, onSelect, onBaseLayerLoading }) {
    const baseLayers = createBaseLayers();
    let activeBase = baseLayers[baseLayer] ? baseLayer : 'streets';
    let swapToken = 0;

    const map = L.map(element, { zoomControl: false }).setView(DEFAULT_CENTER, DEFAULT_ZOOM);
    baseLayers[activeBase].forEach((tileLayer) => tileLayer.addTo(map));
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.control.scale({ position: 'bottomleft', imperial: false }).addTo(map);

    const markers = new Map();
    const cards = new Map();
    const group = L.featureGroup().addTo(map);
    const hoverCard = createHoverCard();

    const showHoverCard = (id) => {
        const marker = markers.get(id);

        if (!marker || !group.hasLayer(marker) || !map.getBounds().contains(marker.getLatLng()) || marker.isPopupOpen()) {
            return;
        }

        const point = map.latLngToContainerPoint(marker.getLatLng());
        const rect = element.getBoundingClientRect();
        hoverCard.show(cards.get(id), { x: rect.left + point.x, y: rect.top + point.y });
    };

    map.on('movestart zoomstart popupopen', () => hoverCard.hide());

    features.forEach((feature) => {
        const layer = layers[feature.layer];
        const marker = L.marker([feature.latitude, feature.longitude], {
            icon: pinIcon(feature, layer),
            title: feature.title,
            riseOnHover: true,
            zIndexOffset: feature.layer === 'campuses' ? 1000 : 0,
        });

        cards.set(feature.id, cardHtml(feature, layer));
        marker.on('mouseover', () => showHoverCard(feature.id));
        marker.on('mouseout', () => hoverCard.hide());
        marker.bindPopup(cardHtml(feature, layer, { withActions: true }), { className: 'gis-popup', closeButton: true, autoPanPadding: [40, 40], maxWidth: 320 });
        marker.on('click', () => {
            hoverCard.hide();
            map.flyTo(marker.getLatLng(), Math.max(map.getZoom(), FOCUS_ZOOM), { duration: 0.8 });
            onSelect(feature.id);
        });

        markers.set(feature.id, marker);
        group.addLayer(marker);
    });

    const fitVisible = ({ animate = true } = {}) => {
        const bounds = group.getBounds();

        if (!bounds.isValid()) {
            map.flyTo(DEFAULT_CENTER, DEFAULT_ZOOM, { animate, duration: 0.45 });

            return;
        }

        const target = bounds.pad(0.25);

        if (animate) {
            map.flyToBounds(target, { maxZoom: 13, duration: 0.45 });
        } else {
            map.fitBounds(target, { maxZoom: 13 });
        }
    };

    fitVisible({ animate: false });

    return {
        show(visibleIds, { refit = true } = {}) {
            markers.forEach((marker, id) => {
                const shouldShow = visibleIds.has(id);

                if (shouldShow && !group.hasLayer(marker)) {
                    group.addLayer(marker);
                } else if (!shouldShow && group.hasLayer(marker)) {
                    marker.closePopup();
                    hoverCard.hide();
                    group.removeLayer(marker);
                }
            });

            if (refit) {
                fitVisible();
            }
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
            showHoverCard(id);
        },
        clearPreview() {
            hoverCard.hide();
        },
        fitVisible,
        /**
         * Swap base maps without a blank flash: keep the old tiles until the new ones finish loading.
         */
        setBaseLayer(name) {
            if (!baseLayers[name] || name === activeBase) {
                return;
            }

            const token = ++swapToken;
            const next = baseLayers[name];
            const [primary] = next;
            activeBase = name;

            next.forEach((tileLayer) => {
                tileLayer.addTo(map);
                tileLayer.bringToFront();
            });
            onBaseLayerLoading(true);

            const finish = () => {
                // A newer switch supersedes this one; it will clean up when its own tiles arrive.
                if (token !== swapToken) {
                    return;
                }

                Object.entries(baseLayers)
                    .filter(([key]) => key !== activeBase)
                    .flatMap(([, tileLayers]) => tileLayers)
                    .forEach((tileLayer) => map.removeLayer(tileLayer));
                onBaseLayerLoading(false);
            };

            if (!primary.isLoading()) {
                finish();

                return;
            }

            primary.once('load', finish);
            window.setTimeout(finish, BASE_LAYER_SWAP_TIMEOUT);
        },
        invalidateSize() {
            map.invalidateSize();
        },
    };
}
