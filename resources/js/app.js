import Alpine from 'alpinejs';

window.Alpine = Alpine;

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

Alpine.start();
