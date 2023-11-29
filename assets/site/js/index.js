import * as Sentry from '@sentry/browser';
import { RewriteFrames } from '@sentry/integrations';
import Translator from 'bazinga-translator';

Sentry.init({
    dsn: SENTRY_DSN,
    environment: APP_ENV,
    integrations: [new RewriteFrames()]
});
require('../../js/Routing');

$.ajaxSetup({
    dataType: 'json',
    contentType: 'application/json',
    headers: {
        'Content-Language': LOCALE,
    },
})

// import Core files
import './Core/jquery.counterup.min'
import './Core/jquery.meanmenu'
import './Core/jquery.scrollUp'
import './Core/theme';
import './Controller/index'
