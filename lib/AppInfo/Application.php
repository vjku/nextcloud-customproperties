<?php
declare(strict_types=1);

namespace OCA\CustomProperties\AppInfo;

use OCA\CustomProperties\Listener\FileEventsListener;
use OCA\CustomProperties\Listener\LoadAdditionalScriptsListener;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Files\Events\Node\NodeCreatedEvent;
use OCP\Files\Events\Node\NodeDeletedEvent;
use OCP\Files\Events\Node\NodeRenamedEvent;
use OCP\Files\Events\NodeAddedToCache;

class Application extends App implements IBootstrap
{
    const APP_ID = 'customproperties';

    public function __construct(array $urlParams = [])
    {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void
    {
        $context->registerEventListener(
            LoadAdditionalScriptsEvent::class,
            LoadAdditionalScriptsListener::class
        );
        $context->registerEventListener(
            NodeRenamedEvent::class,
            FileEventsListener::class
        );
        $context->registerEventListener(
            NodeDeletedEvent::class,
            FileEventsListener::class
        );
    }

    public function boot(IBootContext $context): void
    {

    }
}

