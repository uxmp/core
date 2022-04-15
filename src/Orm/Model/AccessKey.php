<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Uxmp\Core\Orm\Repository\AccessKeyRepository;

#[ORM\Entity(repositoryClass: AccessKeyRepository::class)]
#[ORM\Table(name: 'access_key')]
class AccessKey implements AccessKeyInterface
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $type_id = 0;

    /**
     * @var array{
     *  accessKey?: string
     * } $config
     */
    #[ORM\Column(type: Types::JSON)]
    private array $config = [];

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $active = false;

    #[ORM\Column(type: Types::INTEGER)]
    private int $user_id = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private UserInterface $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTypeId(): int
    {
        return $this->type_id;
    }

    public function setTypeId(int $typeId): AccessKeyInterface
    {
        $this->type_id = $typeId;
        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): AccessKeyInterface
    {
        $this->active = $active;
        return $this;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): AccessKeyInterface
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return array{
     *  accessToken?: string
     * }
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): AccessKeyInterface
    {
        $this->config = $config;
        return $this;
    }
}
