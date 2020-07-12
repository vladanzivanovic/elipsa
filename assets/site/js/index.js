import * as Sentry from '@sentry/browser';
import { RewriteFrames } from '@sentry/integrations';

Sentry.init({
    dsn: 'https://6ed201f868844b3fbc0ef5ffdbcc3187@o419240.ingest.sentry.io/5330528',
    integrations: [new RewriteFrames()]
});
require('../../js/Routing');

// import Core files
import './Core/jquery.counterup.min'
import './Core/jquery.meanmenu'
import './Core/jquery.scrollUp'
import './Core/theme';
import './Controller/index'
