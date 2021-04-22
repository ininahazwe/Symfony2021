<?php

namespace App\EventSubscriber;

use App\Utils\LogoutUserTrait;
use App\Event\AskForPasswordConfirmationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AskForPasswordConfirmationSubscriber implements EventSubscriberInterface
{
    use LogoutUserTrait;

    private RequestStack $requestStack;

    private Session $session;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        RequestStack $requestStack,
        Session $session,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
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
        $this->sendJsonResponse(true);
    }

    public function sendJsonResponse(bool $isUserDeauthenticated = false): void
    {
        if($isUserDeauthenticated){
            $request = $this->requestStack->getCurrentRequest();

            if(!$request){
                return;
            }
            $response = $this->logoutUser(
                $request,
                $this->session,
                $this->tokenStorage,
                'danger',
                'Vous avez été déconnecté par mesure de sécurité car 3 mots de passes invalides ont été saisis lors de la confirmation du mot de passe.',
                false,
                true
            );
            $response->send();

            exit();
        }

        $response = new JsonResponse([
            'is_password_confirmed' => false
        ]);

        $response->send();

        exit();
    }
}