<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity="Post", inversedBy="categories")
     * @ORM\JoinTable(name="categories_posts")
     */
    private $posts;

    public function __construct($category)
    {
        $this->category = $category;
        $this->posts = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function addPost(Post $post)
    {
        $this->posts[] = $post;
    }
}