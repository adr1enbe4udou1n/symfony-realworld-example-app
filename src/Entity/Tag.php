<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table(name: 'public.tags')]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public ?int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public string $name;

    /**
     * @var Collection|Article[]
     */
    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'tags')]
    #[ORM\JoinTable(name: 'article_tag')]
    public Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }
}
