<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FriendshipRepository")
 * @ORM\Table(name="Friendship",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="friendship",
 *              columns={"ask_id", "receive_id"}
 *          )
 *      }
 * )
 */
class Friendship
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="friendshipsAsks")
     * @ORM\JoinColumn(name="ask_id", referencedColumnName="id", nullable=false)
     */
    private $ask;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="friendshipsReceives")
     * @ORM\JoinColumn(name="receive_id", referencedColumnName="id", nullable=false)
     */
    private $receive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->valide = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsk(): ?User
    {
        return $this->ask;
    }

    public function setAsk(?User $ask): self
    {
        $this->ask = $ask;

        return $this;
    }

    public function getReceive(): ?User
    {
        return $this->receive;
    }

    public function setReceive(?User $receive): self
    {
        $this->receive = $receive;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getValide(): ?int
    {
        return $this->valide;
    }

    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }
}
