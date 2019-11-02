<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User extends AbstractController implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message = "Email {{ value }} non valide."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Your username must be at least {{ limit }} characters long",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotBlank(message="Le nom d'utilisateur ne peut pas être vide")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"username"})
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Posts", mappedBy="user", cascade={"remove"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Friendship", mappedBy="ask", orphanRemoval=true)
     */
    private $friendshipsAsks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Friendship", mappedBy="receive", orphanRemoval=true)
     */
    private $friendshipsReceives;

    /**
     * Many User have Many Friendships.
     * @ORM\ManyToMany(targetEntity="Friendship", cascade={"remove"})
     * @ORM\JoinTable(name="users_friendships",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id",  onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friendship_id", referencedColumnName="id",  onDelete="CASCADE")}
     *      )
     */
    private $friendships;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="friendsTarget")
     */
    private $friends;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="friends")
     */
    private $friendsTarget;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group", mappedBy="author", orphanRemoval=true)
     */
    private $authorGroup;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", mappedBy="member")
     */
    private $groups;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->valide = 1;
        $this->posts = new ArrayCollection();
        $this->friendshipsAsks = new ArrayCollection();
        $this->friendshipsReceive = new ArrayCollection();
        $this->friendships = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->friendsTarget = new ArrayCollection();
        $this->authorGroup = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = ucfirst($username);

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Posts[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Posts $post): self
    {
        if ($this->posts->contains($post)) {
            $this->posts->removeElement($post);
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getFriendshipsAsks(): Collection
    {
        return $this->friendshipsAsks;
    }

    public function addFriendshipsAsk(Friendship $friendshipsAsk): self
    {
        if (!$this->friendshipsAsks->contains($friendshipsAsk)) {
            $this->friendshipsAsks[] = $friendshipsAsk;
            $friendshipsAsk->setAsk($this);
        }

        return $this;
    }

    public function removeFriendshipsAsk(Friendship $friendshipsAsk): self
    {
        if ($this->friendshipsAsks->contains($friendshipsAsk)) {
            $this->friendshipsAsks->removeElement($friendshipsAsk);
            // set the owning side to null (unless already changed)
            if ($friendshipsAsk->getAsk() === $this) {
                $friendshipsAsk->setAsk(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Friendship[]
     */
    public function getFriendshipsReceives(): Collection
    {
        return $this->friendshipsReceives;
    }

    public function addFriendshipsReceive(Friendship $friendshipsReceive): self
    {
        if (!$this->friendshipsReceives->contains($friendshipsReceive)) {
            $this->friendshipsReceives[] = $friendshipsReceive;
            $friendshipsReceive->setReceive($this);
        }

        return $this;
    }

    public function removeFriendshipsReceive(Friendship $friendshipsReceive): self
    {
        if ($this->friendshipsReceives->contains($friendshipsReceive)) {
            $this->friendshipsReceives->removeElement($friendshipsReceive);
            // set the owning side to null (unless already changed)
            if ($friendshipsReceive->getReceive() === $this) {
                $friendshipsReceive->setReceive(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Friendship[]
     */
    public function getFriendships(): Collection
    {
        return $this->friendships;
    }

    public function addFriendship(Friendship $friendship)
    {
        if (!$this->friendships->contains($friendship)) {
            $this->friendships[] = $friendship;
        }

        return $this;
    }

    public function removeFriendship(Friendship $friendship)
    {
        if ($this->friendships->contains($friendship)) {
            $this->friendships->removeElement($friendship);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFriends(): Collection
    {
        foreach($this->friendsTarget as $friend) {
            $this->addFriend($friend);
        }
        return $this->friends;
    }

    public function addFriend(self $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
        }

        return $this;
    }

    public function removeFriend(self $friend): self
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
            $friend->removeFriendTarget($this);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFriendsTarget(): Collection
    {
        foreach($this->friends as $friend) {
            $this->addFriendTarget($friend);
        }
        return $this->friends;
    }

    public function addFriendTarget(self $friendsTarget): self
    {
        if (!$this->friendsTarget->contains($friendsTarget)) {
            $this->friendsTarget[] = $friendsTarget;
            $friendsTarget->addFriend($this);
        }

        return $this;
    }

    public function removeFriendTarget(self $friendsTarget): self
    {
        if ($this->friendsTarget->contains($friendsTarget)) {
            $this->friendsTarget->removeElement($friendsTarget);
            $friendsTarget->removeFriend($this);
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getAuthorGroup(): Collection
    {
        return $this->authorGroup;
    }

    public function addAuthorGroup(Group $authorGroup): self
    {
        if (!$this->authorGroup->contains($authorGroup)) {
            $this->authorGroup[] = $authorGroup;
            $authorGroup->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthorGroup(Group $authorGroup): self
    {
        if ($this->authorGroup->contains($authorGroup)) {
            $this->authorGroup->removeElement($authorGroup);
            // set the owning side to null (unless already changed)
            if ($authorGroup->getAuthor() === $this) {
                $authorGroup->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addMember($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
            $group->removeMember($this);
        }

        return $this;
    }
}
