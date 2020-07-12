import * as Sentry from '@sentry/browser';
import { RewriteFrames } from '@sentry/integrations';

Sentry.init({
    dsn: 'https://6ed201f868844b3fbc0ef5ffdbcc3187@o419240.ingest.sentry.io/5330528',
    integrations: [new RewriteFrames()]
});

require('../../js/Routing');

import '../vendors/core/core';
import '../vendors/feather-icons/feather';

import './Core';
import './Controller';

