import * as Sentry from '@sentry/browser';
import { RewriteFrames } from '@sentry/integrations';


Sentry.init({
    dsn: SENTRY_DSN,
    integrations: [new RewriteFrames()]
});
require('../../js/Routing');

// import Core files
import './Core/jquery.counterup.min'
import './Core/jquery.meanmenu'
import './Core/jquery.scrollUp'
import './Core/theme';
import './Controller/index'