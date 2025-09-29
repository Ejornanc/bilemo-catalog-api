<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * On User creation, attach the authenticated Client as owner.
 */
class UserClientProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
    ) {
    }

    /**
     * @param User $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data instanceof User && null === $data->getClient()) {
            $client = $this->security->getUser();
            if ($client) {
                $data->setClient($client);
            }
        }

        // Delegate to Doctrine ORM persist processor
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
