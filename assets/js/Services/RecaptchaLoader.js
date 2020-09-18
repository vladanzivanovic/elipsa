class RecaptchaLoader {
    static loadRecaptcha() {
        if (typeof window.recaptcha === 'undefined') {
            const script = document.createElement('script');
            script.src = `//www.google.com/recaptcha/api.js?render=${GOOGLE_RECAPTCHA_KEY_SITE}`;
            script.async = true;
            document.body.append(script);

        }
    }
}

export default RecaptchaLoader;