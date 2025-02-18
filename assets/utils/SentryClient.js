import * as Sentry from "@sentry/browser";

export class SentryClient {
  constructor() {
    if (window.shoko.env === "prod") {
      Sentry.init({
        dsn: window.shoko.sentry_dns,
        environment: window.shoko.env,
        release: "slave-shoko-cosplay@" + window.shoko.version,
        integrations: [
          Sentry.browserTracingIntegration(),
          Sentry.replayIntegration(),
          Sentry.browserProfilingIntegration(),
          Sentry.browserSessionIntegration(),
        ],
        tracesSampleRate: 1.0,
        replaysSessionSampleRate: 0.1,
        replaysOnErrorSampleRate: 1.0,
      })
    }
  }
}
