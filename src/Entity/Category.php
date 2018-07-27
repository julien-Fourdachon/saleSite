<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Product", cascade={"persist", "remove"})
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
     */
    private $newProduct;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categories;

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product): void
    {
        $this->product = $product;
    }

    public function __construct()
    {
        $this->newProduct = new ArrayCollection();

    }


    public function getId()
    {
        return $this->id;
    }



    /**
     * @return Collection|Product[]
     */
    public function getNewProduct(): Collection
    {
        return $this->newProduct;
    }

    public function addNewProduct(Product $newProduct): self
    {
        if (!$this->newProduct->contains($newProduct)) {
            $this->newProduct[] = $newProduct;
            $newProduct->setCategory($this);
        }

        return $this;
    }

    public function removeNewProduct(Product $newProduct): self
    {
        if ($this->newProduct->contains($newProduct)) {
            $this->newProduct->removeElement($newProduct);
            // set the owning side to null (unless already changed)
            if ($newProduct->getCategory() === $this) {
                $newProduct->setCategory(null);
            }
        }

        return $this;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(string $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

}
