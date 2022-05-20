<?php

namespace App\Entity;

use App\Repository\PlayersDeathsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayersDeathsRepository::class)
 * @ORM\Table( 
 *      indexes={@ORM\Index(name="idx_player_death_nome", columns={"nome"})}
 * )  
 */
class PlayersDeaths
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
     * @ORM\Column(type="datetime")
     */
    private $dataMorte;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $killedBy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkKilledBy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkKilledByTwo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Players", inversedBy="deaths", cascade={"persist"})
     * @ORM\JoinColumn()
     */
    private $player;    

    public function getPlayer(): ?Players
    {
        return $this->player;
    }

    public function setPlayer(?Players $player): self
    {
        $this->player = $player;

        return $this;
    }    

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

    public function getDataMorte(): ?\DateTimeInterface
    {
        return $this->dataMorte;
    }

    public function setDataMorte(\DateTimeInterface $dataMorte): self
    {
        $this->dataMorte = $dataMorte;

        return $this;
    }

    public function getKilledBy(): ?string
    {
        return $this->killedBy;
    }

    public function setKilledBy(string $killedBy): self
    {
        $this->killedBy = $killedBy;

        return $this;
    }

    public function getLinkKilledBy(): ?string
    {
        return $this->linkKilledBy;
    }

    public function setLinkKilledBy(?string $linkKilledBy): self
    {
        $this->linkKilledBy = $linkKilledBy;

        return $this;
    }

    public function getLinkKilledByTwo(): ?string
    {
        return $this->linkKilledByTwo;
    }

    public function setLinkKilledByTwo(?string $linkKilledByTwo): self
    {
        $this->linkKilledByTwo = $linkKilledByTwo;

        return $this;
    }
}
