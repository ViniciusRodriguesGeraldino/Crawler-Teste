<?php

namespace App\Entity;

use App\Repository\PlayersTop10Repository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayersTop10Repository::class)
 */
class PlayersTop10
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $data;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $exp1;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $exp2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lvl1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lvl2;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $experiencia;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getExp1(): ?string
    {
        return $this->exp1;
    }

    public function setExp1(?string $exp1): self
    {
        $this->exp1 = $exp1;

        return $this;
    }

    public function getExp2(): ?string
    {
        return $this->exp2;
    }

    public function setExp2(?string $exp2): self
    {
        $this->exp2 = $exp2;

        return $this;
    }

    public function getLvl1(): ?int
    {
        return $this->lvl1;
    }

    public function setLvl1(?int $lvl1): self
    {
        $this->lvl1 = $lvl1;

        return $this;
    }

    public function getLvl2(): ?int
    {
        return $this->lvl2;
    }

    public function setLvl2(?int $lvl2): self
    {
        $this->lvl2 = $lvl2;

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
