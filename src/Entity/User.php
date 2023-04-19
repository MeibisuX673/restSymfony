<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints AS Assert;

#[ApiResource(
    normalizationContext: ['groups'=>[self::GET_ONE_USER,self::GET_MANY_USER,self::GET_ONE_BASE,self::GET_MANY_BASE]],
    denormalizationContext: ['groups'=>[self::SET_USER]]
)]
#[Patch(security: "object === user", securityMessage: 'У вас нет прав')]
#[Post()]
#[Put(security: "object === user", securityMessage: 'У вас нет прав')]
#[GetCollection]
#[Get]
#[Delete(security: "object === user", securityMessage: 'У вас нет прав')]
#[ORM\Entity]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{

    const GET_ONE_USER = 'GET_ONE_USER';
    const GET_MANY_USER = 'GET_MANY_USER';
    const SET_USER = 'SET_USER';

    #[Assert\Email]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, unique: true, nullable: false)]
    #[Groups([self::GET_ONE_USER,self::SET_USER])]
    private string $email;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Groups([self::GET_ONE_USER,self::SET_USER])]
    public string $firstName;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Groups([self::GET_ONE_USER,self::SET_USER])]
    public string $lastName;

    #[ORM\Column(type: Types::JSON)]
    private iterable $roles = [];

    #[ORM\Column(type: 'string',nullable: false)]
    #[Groups([self::SET_USER])]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $password;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: Brand::class, cascade: ['remove'])]
    #[Groups([self::GET_ONE_USER])]
    public ?Brand $brand;

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function eraseCredentials()
    {

    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self{
        $this->password = $password;
        return $this;
    }
}