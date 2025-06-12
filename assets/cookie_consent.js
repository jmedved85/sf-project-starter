/**
 * Cookie consent manager for GDPR compliance
 */
export function initCookieConsent() {
    const cookieBanner = document.querySelector('#cookie-consent-banner');
    const acceptAllButton = document.querySelector('#cookie-accept-all');
    const acceptNecessaryButton = document.querySelector('#cookie-accept-necessary');
    const settingsButton = document.querySelector('#cookie-settings');
    const footerSettingsButton = document.querySelector('#footer-cookie-settings');
    const saveSettingsButton = document.querySelector('#saveCookieSettings');
    const modal = new bootstrap.Modal(document.querySelector('#cookieSettingsModal'));

    const consentCookieName = 'cookie_consent';
    const cookieExpiration = 180; // days

    // Set initial banner state
    cookieBanner.style.position = 'fixed';
    cookieBanner.style.bottom = '0';
    cookieBanner.style.left = '0';
    cookieBanner.style.right = '0';
    cookieBanner.style.transform = 'translateY(100%)';
    cookieBanner.style.transition = 'transform 1s ease-in-out, opacity 1s ease-in-out';
    cookieBanner.style.display = 'block';
    cookieBanner.style.opacity = '0';

    // Check if consent cookie exists
    const checkConsent = () => {
        const consent = getCookie(consentCookieName);

        if (!consent) {
            showBanner();
        }
    };

    // Show the cookie consent banner
    const showBanner = () => {
        cookieBanner.style.display = 'block';

        // Trigger reflow to ensure transition works
        void cookieBanner.offsetWidth;

        cookieBanner.style.opacity = '1';
        cookieBanner.style.transform = 'translateY(0)';
    };

    // Hide the cookie consent banner
    const hideBanner = () => {
        cookieBanner.style.transform = 'translateY(100%)';
        cookieBanner.style.opacity = '0';

        // Remove from DOM after animation completes
        setTimeout(() => {
            cookieBanner.style.display = 'none';
        }, 1000);
    };

    // Set consent cookie
    const setConsentCookie = (consent) => {
        const expirationDate = new Date();
        expirationDate.setDate(expirationDate.getDate() + cookieExpiration);

        document.cookie = `${consentCookieName}=${JSON.stringify(consent)};expires=${expirationDate.toUTCString()};path=/;SameSite=Lax;Secure`;

        hideBanner();
    };

    // Helper to get cookie value
    const getCookie = (name) => {
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));

        if (match) {
            try {
                return JSON.parse(match[2]);
            } catch (e) {
                return match[2];
            }
        }

        return null;
    };

    // Event listeners
    if (acceptAllButton) {
        acceptAllButton.addEventListener('click', () => {
            setConsentCookie({
                necessary: true,
                analytics: true,
                marketing: true,
                timestamp: new Date().toISOString()
            });
        });
    }

    if (acceptNecessaryButton) {
        acceptNecessaryButton.addEventListener('click', () => {
            setConsentCookie({
                necessary: true,
                analytics: false,
                marketing: false,
                timestamp: new Date().toISOString()
            });
        });
    }

    if (settingsButton) {
        settingsButton.addEventListener('click', () => {
            modal.show();
        });
    }

    if (saveSettingsButton) {
        saveSettingsButton.addEventListener('click', () => {
            const analyticsConsent = document.getElementById('analyticsCookies').checked;
            const marketingConsent = document.getElementById('marketingCookies').checked;

            setConsentCookie({
                necessary: true,
                analytics: analyticsConsent,
                marketing: marketingConsent,
                timestamp: new Date().toISOString()
            });
            
            modal.hide();
        });
    }

    if (footerSettingsButton) {
        footerSettingsButton.addEventListener('click', (event) => {
            event.preventDefault();

            // Before showing the modal, load the current cookie values
            const consent = getCookie(consentCookieName);

            if (consent) {
                // Pre-fill the checkbox values based on stored consent
                if (document.getElementById('analyticsCookies')) {
                    document.getElementById('analyticsCookies').checked = consent.analytics;
                }
                if (document.getElementById('marketingCookies')) {
                    document.getElementById('marketingCookies').checked = consent.marketing;
                }
            }

            modal.show();
        });
    }

    // Initialize
    checkConsent();
}