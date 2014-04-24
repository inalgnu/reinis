<?php

namespace SensioLabs\JobBoardBundle\Security\Authorization\Voter;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiHostVoter implements VoterInterface
{
    protected $requestStack;
    private $whiteListHosts;
    private $env;

    public function __construct(RequestStack $requestStack, $env, array $whiteListHosts = array())
    {
        $this->requestStack  = $requestStack;
        $this->whiteListHosts = $whiteListHosts;
        $this->env = $env;
    }

    public function supportsAttribute($attribute)
    {
        return $attribute === 'api_job_random';
    }

    public function supportsClass($class)
    {
        return true;
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$this->supportsAttribute($request->attributes->get('_route')) ) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if ($this->env === 'dev') {
            return VoterInterface::ACCESS_GRANTED;
        }

        if (!in_array($request->getHost(), $this->whiteListHosts)) {
            return VoterInterface::ACCESS_DENIED;
        }

        return VoterInterface::ACCESS_GRANTED;
    }
}
