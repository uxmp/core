<?php

declare(strict_types=1);

namespace Uxmp\Core\Orm\Model;

/**
 * @Entity(repositoryClass="\Uxmp\Core\Orm\Repository\RadioStationRepository")
 * @Table(name="radiostation")
 */
class RadioStation implements RadioStationInterface
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    private int $id;

    /**
     * @Column(type="string")
     */
    private string $name = '';

    /**
     * @Column(type="string")
     */
    private string $url = '';

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;
        return $this;
    }
}
