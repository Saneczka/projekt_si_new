<?php
/**
 * LoginFormAuthenticator
 */

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * @var \App\Repository\UserRepository
     */
    private $userRepository;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var \Symfony\Component\Security\Core\User\UserInterface\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * LoginFormAuthenticator constructor.
     * @param \App\Repository\UserRepository                                                   $userRepository
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface                       $urlGenerator
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface                       $csrfTokenManager
     * @param \Symfony\Component\Security\Core\User\UserInterface\UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserRepository $userRepository, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    /**
     * @param array                                                       $credentials
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $userProvider
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->userRepository->findOneBy(['username' => $credentials['username']]);

        if (!$user) {
            throw new UsernameNotFoundException('Username could not be found.');
        }

        return $user;
    }

    /**
     * @param array         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param mixed $credentials
     *
     * @return string|null
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request                            $request
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     * @param string                                                               $providerKey
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('album_index'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
