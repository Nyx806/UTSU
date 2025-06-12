<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginAuthenticator extends AbstractAuthenticator {
  private UrlGeneratorInterface $urlGenerator;
  private UserRepository $userRepository;

  public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository) {
    $this->urlGenerator = $urlGenerator;
    $this->userRepository = $userRepository;
  }

  public function supports(Request $request): ?bool {
    return $request->getPathInfo() === '/api/login' && $request->isMethod('POST');
  }

  public function authenticate(Request $request): Passport {
    $username = $request->request->get('username');
    $password = $request->request->get('password');

    if (!$username || !$password) {
      throw new CustomUserMessageAuthenticationException('Username and password are required.');
    }

    // Vérifier si l'utilisateur existe.
    $user = $this->userRepository->findOneBy(['username' => $username]);
    if (!$user) {
      throw new CustomUserMessageAuthenticationException('Invalid username or password.');
    }

    // Retourner un Passport avec les badges nécessaires.
    return new Passport(
          new UserBadge(
              $username,
              function ($userIdentifier) {
                  return $this->userRepository->findOneBy(['username' => $userIdentifier]);
              }
          ),
          new PasswordCredentials($password)
      );
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
    // Rediriger vers la page d'accueil après succès.
    return new RedirectResponse($this->urlGenerator->generate('home_index'));
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
    return new JsonResponse(
          [
            'error' => 'Authentication failed',
            'message' => $exception->getMessage(),
          ],
          Response::HTTP_UNAUTHORIZED
      );
  }

}
