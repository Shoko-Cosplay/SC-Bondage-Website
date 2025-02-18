<?php

use App\Infrastructure\CrowdsecService\Bouncer;
use App\Infrastructure\CrowdsecService\Constants;
use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {

    $settings = new \App\Infrastructure\CrowdsecService\Settings(__DIR__,$context);
    $settings = $settings->settings();
    if (isset($settings['bouncing_level']) && Constants::BOUNCING_LEVEL_DISABLED !== $settings['bouncing_level']) {
        $bouncer = new Bouncer($settings);
        $bouncer->run();
    }
    $vault = new \App\Infrastructure\Vault\VaultClient($context['APP_ENV']);
    $vault->unlock();

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
