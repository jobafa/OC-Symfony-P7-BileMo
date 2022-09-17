<?php

Namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
//use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Groups;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "detailUser",
 *          parameters = { "id" = "expr(object.getId())" }
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="get:users")
 * )
 *
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "deleteUser",
 *          parameters = { "id" = "expr(object.getId())" },
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="get:users", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 * )
 *
 * @Hateoas\Relation(
 *      "post",
 *      href = @Hateoas\Route(
 *          "createUser",
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="get:users", excludeIf = "expr(not is_granted('ROLE_ADMIN'))"),
 * )
 *
 * @Hateoas\Relation(
 *      "userslist",
 *      href = @Hateoas\Route(
 *          "app_clientusers",
 *      ),
 *      exclusion = @Hateoas\Exclusion(groups="get:users"),
 * )
 *
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("get:users", "get:clients")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @NotBlank
     * @Assert\Email(
     *      message="Cette adresse n'est pas adresse email valide !"
     * )
     * 
     * @Groups("get:users", "get:clients")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
    * @NotBlank(message="Ce champ ne peut pas être vide !")
     * @Assert\Length(
     *      min="5",
     *      max="30",
     *      minMessage="Ce champ doit contenir au moins 5 caractéres !",
     *      maxMessage="Ce champ doit contenir au maximum 30 caractéres !"
     * )
     *
     * @Groups("get:users", "get:clients")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @NotBlank(message="Ce champ ne peut pas être vide !")
     * @Assert\Length(
     *      min="5",
     *      max="30",
     *      minMessage="Ce champ doit contenir au moins 5 caractéres !",
     *      maxMessage="Ce champ doit contenir au maximum 30 caractéres !"
     * 
     * )
     *
     * @Groups("get:users", "get:clients")
     */
    private $firstname;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("get:users", "get:clients")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("get:users", "get:clients")
     */
    private $client;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("get:users", "get:clients")
     * @Since("2.0")
     */
    private $comment;

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

    public function getLastName(): ?string
    {
        return $this->lastname;
    }

    public function setLastName(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    public function setFirstName(?string $firstname): self
    {
        //$this->N = $N;
        $this->firstname = $firstname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
