<?php

namespace App\Entity;

use App\Repository\PlayersRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=PlayersRepository::class)
 * @ORM\Table( 
 *      indexes={@ORM\Index(name="idx_nome", columns={"nome"})}
 * )  
 */
class Players
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vocacao;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataConsulta;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataCriacao;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataUltimoLogin;
    
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomeGuild;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $linkGuild;   

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataAccCriada;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlayersDeaths", mappedBy="deaths", cascade={"persist"}, orphanRemoval=true)
     */
    private $player;    

    public function __construct()
    {
        $this->deaths = new ArrayCollection();
    }    

    /**
     * @return Collection|PlayersDeaths[]
     */
    public function getPlayersDeaths(): Collection
    {
        return $this->deaths;
    }

    public function addPlayersDeaths(PlayersDeaths $deaths): self
    {
        if (!$this->deaths->contains($deaths)) {
            $this->deaths[] = $deaths;
            $deaths->setPlayer($this);
        }

        return $this;
    }

    public function removePlayersDeaths(PlayersDeaths $deaths): self
    {
        if ($this->deaths->contains($deaths)) {
            $this->deaths->removeElement($deaths);
            // set the owning side to null (unless already changed)
            if ($deaths->getPlayer() === $this) {
                $deaths->setPlayer(null);
            }
        }

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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getVocacao(): ?string
    {
        return $this->vocacao;
    }

    public function setVocacao(?string $vocacao): self
    {
        $this->vocacao = $vocacao;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

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

    public function getDataCriacao(): ?\DateTimeInterface
    {
        return $this->dataCriacao;
    }

    public function setDataCriacao(?\DateTimeInterface $dataCriacao): self
    {
        $this->dataCriacao = $dataCriacao;

        return $this;
    }    

    public function getDataUltimoLogin(): ?\DateTimeInterface
    {
        return $this->dataUltimoLogin;
    }

    public function setDataUltimoLogin(?\DateTimeInterface $dataUltimoLogin): self
    {
        $this->dataUltimoLogin = $dataUltimoLogin;

        return $this;
    }   

    public function getDataAccCriada(): ?\DateTimeInterface
    {
        return $this->dataAccCriada;
    }

    public function setDataAccCriada(?\DateTimeInterface $dataAccCriada): self
    {
        $this->dataAccCriada = $dataAccCriada;

        return $this;
    }       
    
    public function getNomeGuild(): ?string
    {
        return $this->nomeGuild;
    }

    public function setNomeGuild(?string $nomeGuild): self
    {
        $this->nomeGuild = $nomeGuild;

        return $this;
    }
    
    public function getLinkGuild(): ?string
    {
        return $this->linkGuild;
    }

    public function setLinkGuild(?string $linkGuild): self
    {
        $this->linkGuild = $linkGuild;

        return $this;
    }    

}
