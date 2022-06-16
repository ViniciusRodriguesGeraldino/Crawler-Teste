<?php

namespace App\Entity;

use App\Repository\PlayersOnlineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayersOnlineRepository::class)
 * @ORM\Table( 
 *      indexes={@ORM\Index(name="idx_player_online_nome", columns={"nome"})}
 * )  
 */
class PlayersOnline
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataConsulta;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalHorasOnline;

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

    public function getDataConsulta(): ?\DateTimeInterface
    {
        return $this->dataConsulta;
    }

    public function setDataConsulta(?\DateTimeInterface $dataConsulta): self
    {
        $this->dataConsulta = $dataConsulta;

        return $this;
    }

    public function getTotalHorasOnline(): ?int
    {
        return $this->totalHorasOnline;
    }

    public function setTotalHorasOnline(?int $totalHorasOnline): self
    {
        $this->totalHorasOnline = $totalHorasOnline;

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
