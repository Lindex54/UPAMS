import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Coordinate the dependent District-to-Village dropdowns against the application's local APIs.
Alpine.data('ugandaLocationSelector', (configuration) => ({
    selected: {
        district: String(configuration.selected.district ?? ''),
        county: String(configuration.selected.county ?? ''),
        subCounty: String(configuration.selected.subCounty ?? ''),
        parish: String(configuration.selected.parish ?? ''),
        village: String(configuration.selected.village ?? ''),
    },
    options: {
        counties: configuration.options.counties ?? [],
        subCounties: configuration.options.subCounties ?? [],
        parishes: configuration.options.parishes ?? [],
        villages: configuration.options.villages ?? [],
    },
    loading: { counties: false, subCounties: false, parishes: false, villages: false },
    messages: { counties: '', subCounties: '', parishes: '', villages: '' },
    // Per-level counters prevent a slower, outdated response from replacing a newer selection.
    requestSequence: { counties: 0, subCounties: 0, parishes: 0, villages: 0 },

    districtChanged() {
        this.reset(['counties', 'subCounties', 'parishes', 'villages']);
        this.selected.county = '';
        this.selected.subCounty = '';
        this.selected.parish = '';
        this.selected.village = '';
        return this.load('counties', configuration.urls.counties, this.selected.district);
    },

    countyChanged() {
        this.reset(['subCounties', 'parishes', 'villages']);
        this.selected.subCounty = '';
        this.selected.parish = '';
        this.selected.village = '';
        return this.load('subCounties', configuration.urls.subCounties, this.selected.county);
    },

    subCountyChanged() {
        this.reset(['parishes', 'villages']);
        this.selected.parish = '';
        this.selected.village = '';
        return this.load('parishes', configuration.urls.parishes, this.selected.subCounty);
    },

    parishChanged() {
        this.reset(['villages']);
        this.selected.village = '';
        return this.load('villages', configuration.urls.villages, this.selected.parish);
    },

    reset(levels) {
        levels.forEach((level) => {
            this.requestSequence[level] += 1;
            this.options[level] = [];
            this.messages[level] = '';
            this.loading[level] = false;
        });
    },

    /**
     * Load active children for one parent from a database-backed Laravel endpoint.
     */
    async load(level, urlTemplate, parentId) {
        if (!parentId) {
            return;
        }

        const sequence = ++this.requestSequence[level];
        this.loading[level] = true;
        this.messages[level] = '';

        try {
            const response = await fetch(urlTemplate.replace('__PARENT__', parentId), {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`Location request failed with status ${response.status}`);
            }

            const payload = await response.json();

            // Ignore a response when the user changed the parent while its request was in flight.
            if (sequence !== this.requestSequence[level]) {
                return;
            }

            this.options[level] = Array.isArray(payload.data) ? payload.data : [];
            this.messages[level] = this.options[level].length === 0
                ? `No active ${this.label(level)} found for this selection.`
                : '';
        } catch (error) {
            if (sequence === this.requestSequence[level]) {
                this.options[level] = [];
                this.messages[level] = `Unable to load ${this.label(level)}. Please try again.`;
            }
        } finally {
            if (sequence === this.requestSequence[level]) {
                this.loading[level] = false;
            }
        }
    },

    label(level) {
        return {
            counties: 'counties',
            subCounties: 'sub-counties',
            parishes: 'parishes',
            villages: 'villages',
        }[level];
    },
}));

// Turn a latitude/longitude pair into OpenStreetMap previews and external map links.
Alpine.data('gpsLocationPreview', (configuration = {}) => ({
    latitude: String(configuration.latitude ?? ''),
    longitude: String(configuration.longitude ?? ''),
    mapOpen: false,

    get coordinates() {
        if (String(this.latitude).trim() === '' || String(this.longitude).trim() === '') {
            return null;
        }

        const latitude = Number(this.latitude);
        const longitude = Number(this.longitude);

        if (!Number.isFinite(latitude) || !Number.isFinite(longitude) || Math.abs(latitude) > 90 || Math.abs(longitude) > 180) {
            return null;
        }

        return { latitude, longitude };
    },

    get hasLocation() {
        return this.coordinates !== null;
    },

    get coordinateLabel() {
        return this.hasLocation
            ? `${this.coordinates.latitude.toFixed(6)}, ${this.coordinates.longitude.toFixed(6)}`
            : '';
    },

    embedUrl(span = 0.004) {
        if (!this.hasLocation) {
            return 'about:blank';
        }

        const { latitude, longitude } = this.coordinates;
        const boundingBox = [longitude - span, latitude - span, longitude + span, latitude + span].map((edge) => edge.toFixed(7)).join(',');

        return `https://www.openstreetmap.org/export/embed.html?bbox=${boundingBox}&layer=mapnik&marker=${latitude},${longitude}`;
    },

    get googleMapsUrl() {
        return this.hasLocation
            ? `https://www.google.com/maps/search/?api=1&query=${this.coordinates.latitude},${this.coordinates.longitude}`
            : '#';
    },

    get openStreetMapUrl() {
        return this.hasLocation
            ? `https://www.openstreetmap.org/?mlat=${this.coordinates.latitude}&mlon=${this.coordinates.longitude}#map=17/${this.coordinates.latitude}/${this.coordinates.longitude}`
            : '#';
    },

    openMap() {
        if (this.hasLocation) {
            this.mapOpen = true;
        }
    },

    closeMap() {
        this.mapOpen = false;
    },
}));

Alpine.start();
