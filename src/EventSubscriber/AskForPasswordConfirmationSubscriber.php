<?php

namespace App\EventSubscriber;

use App\Event\AskForPasswordConfirmationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AskForPasswordConfirmationSubscriber implements EventSubscriberInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AskForPasswordConfirmationEvents::MODAL_DISPLAY => 'onModalDisplay',
            AskForPasswordConfirmationEvents::PASSWORD_INVALID => 'onPasswordInvalid',
            AskForPasswordConfirmationEvents::SESSION_INVALIDATE => 'onSessionInvalidate'
        ];
    }

    public function onModalDisplay(AskForPasswordConfirmationEvents $events):void
    {
        $this->sendJsonResponse();
    }

    public function onPasswordInvalid(AskForPasswordConfirmationEvents $events):void
    {
        $this->sendJsonResponse();
    }

    public function onSessionInvalidate(AskForPasswordConfirmationEvents $events):void
    {
        $loginRoute = $this->urlGenerator->generate('app_login');
        $this->sendJsonResponse($loginRoute);
    }

    public function sendJsonResponse(?string $loginRoute = null): void
    {
        $data = [
            'is_password_confirmed' => false,
            'status_code'           => 200
        ];

        $status = 200;

        if($loginRoute) {
            $data['login_route'] = $loginRoute;
            $data['status_code'] = 302;
            $status = 302;
        }

        $response = new JsonResponse($data, $status);

        $response->send();

        exit();
    }
}