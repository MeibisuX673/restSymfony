<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use \Symfony\Component\Validator\Constraints AS Assert;


class BaseEntity
{
    const GET_MANY_BASE = 'GET_MANY_BASE';
    const GET_ONE_BASE = 'GET_ONE_BASE';

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    #[Groups([self::GET_ONE_BASE,self::GET_MANY_BASE])]
    protected int $id;

    #[Groups([self::GET_MANY_BASE,self::GET_ONE_BASE])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE,nullable: true)]
    public \DateTimeInterface $dateCreate;

    #[Groups([self::GET_MANY_BASE,self::GET_ONE_BASE])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE,nullable: true)]
    public \DateTimeInterface $dateUpdate;

    public function getId()
    {
        return $this->id;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(){
        $this->dateCreate = new DateTime('now');
        $this->dateUpdate = new DateTime('now');
    }

    #[ORM\PreUpdate]
    public function setDateUpdate(){
        $this->dateUpdate = new DateTime('now');
    }

}