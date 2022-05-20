<?php

namespace App\Entity;

use App\Repository\PlayersGuildRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayersGuildRepository::class)
 */
class PlayersGuild
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomeRank;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getNomeRank(): ?string
    {
        return $this->nomeRank;
    }

    public function setNomeRank(string $nomeRank): self
    {
        $this->nomeRank = $nomeRank;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }
}
