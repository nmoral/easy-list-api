<?php

namespace App\Security\Voter;

use App\Security\Voter\Extension\VoterExtensionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MainVoter extends Voter
{
    /**
     * @var string
     */
    public const ACTION_CREATE = 'create';

    /**
     * @var string
     */
    public const ACTION_RETRIEVE = 'read';

    /**
     * @var string
     */
    public const ACTION_UPDATE = 'update';

    /**
     * @var string
     */
    public const ACTION_DELETE = 'delete';

    /**
     * @var VoterExtensionInterface[]
     */
    private array $voter = [];

    public function addExtension(VoterExtensionInterface $voter): void
    {
        $this->voter[$voter->getSupportedClass()] = $voter;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        if ($subject instanceof \Traversable) {
            foreach ($subject as $item) {
                $subject = $item;
                break;
            }
        }

        return isset($this->voter[get_class($subject)]);
    }

    /**
     * @param mixed[] $attributes
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if ($subject instanceof \Traversable) {
            foreach ($subject as $item) {
                $decision = parent::vote($token, $item, $attributes);
                if (self::ACCESS_GRANTED !== $decision) {
                    return self::ACCESS_DENIED;
                }
            }

            return self::ACCESS_GRANTED;
        }

        return parent::vote($token, $subject, $attributes);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        return $this->voter[get_class($subject)]->voteOnAttribute($attribute, $subject, $token);
    }
}
