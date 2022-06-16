<?php

namespace App\Entity;

use App\Repository\PlayersHighscoreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayersHighscoreRepository::class)
 */
class PlayersHighscore
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
     * @ORM\Column(type="date")
     */
    private $data;

    /**
     * @ORM\Column(type="integer")
     */
    private $posicaoRank;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $experiencia;

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

    public function getData(): ?\DateTimeInterface
    {
        return $this->data;
    }

    public function setData(\DateTimeInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getPosicaoRank(): ?int
    {
        return $this->posicaoRank;
    }

    public function setPosicaoRank(int $posicaoRank): self
    {
        $this->posicaoRank = $posicaoRank;

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

    public function getExperiencia(): ?string
    {
        return $this->experiencia;
    }

    public function setExperiencia(?string $experiencia): self
    {
        $this->experiencia = $experiencia;

        return $this;
    }
}
