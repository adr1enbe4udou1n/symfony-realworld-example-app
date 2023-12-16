<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Tag\TagListController;
use App\Dto\Tag\TagResponse;
use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[Get(
    name: 'GetTags',
    uriTemplate: '/tags',
    controller: TagListController::class,
    output: TagResponse::class,
    read: false,
    openapi: new Operation(
        summary: 'Get tags.',
        description: 'Get tags. Auth not required',
        tags: ['Tags'],
        security: [],
    )
)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(identifier: false)]
    public ?int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public string $name;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'tags')]
    public $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addTag($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeTag($this);
        }

        return $this;
    }
}
