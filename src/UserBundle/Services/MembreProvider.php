<?php

namespace UserBundle\Services;

use Doctrine\ORM\EntityManager;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User as HWIOAuth;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use UserBundle\Entity\Membre;

class MembreProvider implements UserProviderInterface
{

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * Constructor
	 *
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * IMPLEMENTS UserProviderInterface
	 */

	/**
	 * {@inheritdoc}
	 */
	public function refreshUser(UserInterface $user)
	{
		if(!$user instanceof Membre)
		{
			throw new UnsupportedUserException(sprintf('Instances de "%s" non supportées.', get_class($user)));
		}

		return $this->loadUserByUsername($user->getUsername());
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadUserByUsername($username){
		$membre = $this->em->getRepository('UserBundle:Membre')->findOneBy(array('pseudo' => $username));
		if($membre != null){
			return $membre;
		}
		throw new UsernameNotFoundException(sprintf('Le pseudo "%s" n\'existe pas.', $username));
	}

	/**
	 * {@inheritdoc}
	 */
	public function supportsClass($class){
		return $class === Membre::class;
	}

	/**
	 * IMPLEMENTS OAuthAwareUserProviderInterface
	 */

	/**
	 * {@inheritdoc}
	 */
	public function loadUserByOAuthUserResponse(UserResponseInterface $response)
	{
		$pseudo = $response->getUsername();

		$membre = $this->em->getRepository('UserBundle:Membre')->findOneBy(array('pseudo' => $pseudo));

		if (null === $membre || null === $pseudo) {
			throw new AccountNotLinkedException(sprintf('Le pseudo "%s" n\'a pas été trouvé.', $pseudo));
		}

		return $membre;
	}

}