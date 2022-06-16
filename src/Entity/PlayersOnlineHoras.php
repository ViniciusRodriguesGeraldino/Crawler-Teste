<?php

namespace App\Entity;

use App\Repository\PlayersOnlineHorasRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayersOnlineHorasRepository::class)
 * @ORM\Table( 
 *      indexes={@ORM\Index(name="idx_players_online_horas_nome", columns={"nome"})}
 * )  
 */
class PlayersOnlineHoras
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
     * @ORM\Column(type="date", nullable=true)
     */
    private $dataOnline;

    /**
     * @ORM\Column(type="time")
     */
    private $horaOnline;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $horaOffline;

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

    public function getDataOnline(): ?\DateTimeInterface
    {
        return $this->dataOnline;
    }

    public function setDataOnline(?\DateTimeInterface $dataOnline): self
    {
        $this->dataOnline = $dataOnline;

        return $this;
    }

    public function getHoraOnline(): ?\DateTimeInterface
    {
        return $this->horaOnline;
    }

    public function setHoraOnline(\DateTimeInterface $horaOnline): self
    {
        $this->horaOnline = $horaOnline;

        return $this;
    }

    public function getHoraOffline(): ?\DateTimeInterface
    {
        return $this->horaOffline;
    }

    public function setHoraOffline(?\DateTimeInterface $horaOffline): self
    {
        $this->horaOffline = $horaOffline;

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
