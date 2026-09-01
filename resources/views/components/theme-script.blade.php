<script>
    (() => {
        const storageKey = 'upams-theme';
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

        const isValidTheme = (theme) => theme === 'light' || theme === 'dark';

        const savedTheme = () => {
            try {
                const theme = localStorage.getItem(storageKey);

                return isValidTheme(theme) ? theme : null;
            } catch (error) {
                return null;
            }
        };

        const preferredTheme = () => savedTheme() ?? (systemTheme.matches ? 'dark' : 'light');

        const updateControls = (isDark) => {
            document.querySelectorAll('[data-theme-toggle]').forEach((control) => {
                const label = isDark ? 'Switch to light mode' : 'Switch to dark mode';

                control.setAttribute('aria-label', label);
                control.setAttribute('aria-pressed', String(isDark));
                control.setAttribute('title', label);
            });
        };

        const applyTheme = (theme, persist = false) => {
            const isDark = theme === 'dark';

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.dataset.theme = theme;
            document.documentElement.style.colorScheme = theme;

            document.querySelector('meta[name="theme-color"]')?.setAttribute(
                'content',
                isDark ? '#090f1c' : '#f5f8fc',
            );

            updateControls(isDark);

            if (persist) {
                try {
                    localStorage.setItem(storageKey, theme);
                } catch (error) {
                    // The active theme still applies when storage is unavailable.
                }
            }

            window.dispatchEvent(new CustomEvent('upams-theme-changed', {
                detail: { dark: isDark, theme },
            }));
        };

        applyTheme(preferredTheme());

        window.upamsTheme = {
            current: () => document.documentElement.classList.contains('dark') ? 'dark' : 'light',
            set: (theme) => {
                if (isValidTheme(theme)) {
                    applyTheme(theme, true);
                }
            },
            toggle: () => {
                const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';

                applyTheme(nextTheme, true);
            },
        };

        const handleSystemThemeChange = (event) => {
            if (savedTheme() === null) {
                applyTheme(event.matches ? 'dark' : 'light');
            }
        };

        if (typeof systemTheme.addEventListener === 'function') {
            systemTheme.addEventListener('change', handleSystemThemeChange);
        } else {
            systemTheme.addListener(handleSystemThemeChange);
        }

        document.addEventListener('click', (event) => {
            if (event.target.closest('[data-theme-toggle]')) {
                window.upamsTheme.toggle();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateControls(document.documentElement.classList.contains('dark'));
        }, { once: true });

        window.addEventListener('storage', (event) => {
            if (event.key === storageKey) {
                applyTheme(preferredTheme());
            }
        });
    })();
</script>
