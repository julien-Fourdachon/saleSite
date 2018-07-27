<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Region;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Form\ProductType;
use App\Service\FileUploader;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class ProductController extends Controller
{


    /**
     * @Route("/product/add", name="product.add")
     * @template ("/product/add.html.twig")
     *
     *
     */
    public function add(Request $request, EntityManagerInterface $em, FileUploader $fileUploader)
    {
        $product = new Product();
        $user = $this->getUser ();

        $form = $this->createForm (ProductType::class, $product)
            ->add ("save", SubmitType::class, ["label" => "Ajouter Produit"]);

        $form->handleRequest ($request);
        if ($form->isSubmitted () && $form->isValid ()) {

            $file = $form->get ("pics")->getData ();
            $fileName = $fileUploader->upload ($file);

            $product->setPics ($fileName);

            $product->setUser ($user);
            $em->persist ($product);
            $em->flush ();
            return $this->redirectToRoute ("product.all");
        }

        return ["form" => $form->createView ()];
    }

    /**
     * @Route("", name="product.all")
     */
    public function all(ProductRepository $repository)
    {
        $products = $repository->findAll ();
        return $this->render ("product/all.html.twig", ["products" => $products]);
    }

    /**
     * @Route("/product/show/{product}", name="product.show")
     */

    public function show(Product $product)
    {
        $pro = $product->getUser ();
        return $this->render ("product/show.html.twig", ["product" => $product]);

    }

    /**
     * @Route("/product/update/{product}", name="product.update")
     */

    public function update(Product $product, EntityManagerInterface $em, Request $request, FileUploader $fileUploader)
    {

        $form = $this->createForm (ProductType::class, $product)
            ->add ("update", SubmitType::class, ["label" => "update Product"]);

        $form->handleRequest ($request);
        if ($form->isSubmitted () && $form->isValid ()) {

            $file = $form->get ("pics")->getData ();
            $fileName = $fileUploader->upload ($file);

            $product->setPics ($fileName);

            $em->flush ();
            return $this->redirectToRoute ("product.all");
        }

        if (!$this->isGranted ('edit', $product)) {
            $this->addFlash ('notice', 'hophophop');
            return $this->redirectToRoute ("product.all");

        }
        return $this->render ("/product/update.html.twig", ["form" => $form->createView ()]);

    }

    /**
     * @Route("/product/delete/{product}", name="product.delete")
     */

    public function delete(Product $product)
    {
        if (!$this->isGranted ('edit', $product)) {
            $this->addFlash ('notice', 'hophophop');
            return $this->redirectToRoute ("product.all");
        }
        $em = $this->getDoctrine ()->getManager ();
        $em->remove ($product);
        $em->flush ();
        return $this->redirectToRoute ("product.all");
    }


    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @Route("/moderate/{product}", name="moderate")
     */
    public function moderator(AuthorizationCheckerInterface $authorizationChecker, Product $product, EntityManagerInterface $em)
    {
        if ($authorizationChecker->isGranted ('ROLE_ADMIN')) {
            $product->setAllowed (false);
            $em->flush ();
        }
        dump ($product);
        return $this->redirectToRoute ("product.all");
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, EntityManagerInterface $em)
    {
        $products = $em->getRepository(Product::class)->findAll();
        $form = $this->createFormBuilder ($products)
            ->add ("search", SearchType::class)
            ->add ("region", EntityType::class, array(
                "class" => Region::class,
                "choice_label" => "region"
            ))
            ->add ("category", EntityType::class, array(
                "class" => Category::class,
                "choice_label" => "Categories"
            ))

            ->add ("save", SubmitType::class, ["label" => "Rechercher"])
            ->getForm();

        $form->handleRequest ($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $repo = $em->getRepository (Product::class);
            $file = $form->get ('search')->getData ();
            $prod = $repo->search ($file);

            return $this->render ("/product/all.html.twig", ["products" => $prod]);

        }
        return $this->render ("/search/search.html.twig", ["prout" => $form->createView()]);

    }
}