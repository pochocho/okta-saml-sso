<?php

namespace Pochocho\OktaSamlSso\Events;

use Illuminate\Foundation\Events\Dispatchable;

class OktaWebhookReceived
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        public array $eventMeta,
        public array $eventData
    ) {}
}
