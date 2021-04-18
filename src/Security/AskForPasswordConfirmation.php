<?php

namespace App\Security;

use App\Entity\User;
use App\Event\AskForPasswordConfirmationEvents;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class AskForPasswordConfirmation
{
    private EventDispatcherInterface $eventDispatcher;
    private RequestStack $requestStack;
    private Security $security;

    /**
     * @var Session<mixed>
     */
    private Session $session;
    private UserPasswordEncoderInterface $encoder;

    /**
     * AskForPasswordConfirmation constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param RequestStack $requestStack
     * @param Security $security
     * @param Session<mixed> $session
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        Security $security,
        Session $session,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->encoder = $encoder;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->session = $session;
    }

    /**
     * Displays the password confirmation modal in case of sensitive operation and checks if the entered password is valid.
     *
     * @throws \JsonException
     */
    public function ask(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if(!$request){
            throw new LogicException("Oups, une errreur a eu lieu. Vous ne devrier pas voir ce message");
        }

        if(!$request->headers->get('Confirm-Identity-With-Password')){
            $this->dispatchDisplayModalEvent();
        }

        $this->dispatchPasswordInvalidEventOrContinue($request);
    }

    /**
     * Dispatch a display modal event to display the modal window to confirm the password.
     * @return void
     */
    private function dispatchDisplayModalEvent(): void
    {
        $this->eventDispatcher->dispatch(new AskForPasswordConfirmationEvents(), AskForPasswordConfirmationEvents::MODAL_DISPLAY);
    }

    /**
     * Dispatch a password invalid event if the usr entered an invalid confirmation password.
     * If the confirmation password is valid, then the request continues.
     *
     * @param Request $request
     * @return void
     */
    private function dispatchPasswordInvalidEventOrContinue(Request $request): void
    {
        /**@var string $json */
        $json = $request->getContent();

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if(!array_key_exists('password', $data)){
            throw new HttpException(400, "Le mot de passe doit être saisi.");
        }

        $passwordEntered = $data['password'];

        /** @var User $user */
        $user = $this->security->getUser();

        if(!$this->encoder->isPasswordValid($user, $passwordEntered)){
            $this->invalidateSessionIfThreeInvalidConfirmPassword();

            $this->eventDispatcher->dispatch(new AskForPasswordConfirmationEvents, AskForPasswordConfirmationEvents::PASSWORD_INVALID);
        }

        $this->session->remove('Password-Confirmation-Invalid');
    }

    /**
     *
     * Invalidate the user's session if he enters 3 invalid confirmation passwords.
     *
     * @return void
     */
    private function invalidateSessionIfThreeInvalidConfirmPassword(): void
    {
        if(!$this->session->get('Password-Confirmation-Invalid')){
            $this->session->set('Password-Confirmation-Invalid', 1);
        } else {
            $this->session->set('Password-Confirmation-Invalid', $this->session->get('Password-Confirmation-Invalid') + 1);

            if($this->session->get('Password-Confirmation-Invalid') === 3) {
                $this->session->invalidate();

                $this->session->getFlashBag()->add('danger', 'Vous avez été deconnecté par mesure de sécurité car 3 mots de passe invalides ont été saisis lors de la confirmation du mot de passe.');

                $this->eventDispatcher->dispatch(new AskForPasswordConfirmationEvents, AskForPasswordConfirmationEvents::SESSION_INVALIDATE);
            }
        }
    }
}