<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\ProductService;

class ProductController extends AbstractFOSRestController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Listar Productos.
     * @Rest\Get("/product")
     * 
     * @return Response
    */
    public function getProductsAction(ProductRepository $proRepository)
    {
        return $this->handleView($this->view($proRepository->findAll()));
    }

    /**
     * Crea Producto.
     * @Rest\Post("/product")
     * 
     * @return Response
    */
    public function createProductAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Listar Productos.
     * @Rest\Get("/product/featured")
     * 
     * @return Response
    */
    public function getFeaturedProductsAction(Request $request, ProductRepository $proRepository)
    {
        $currency = $request->query->get('currency');

        $products = $proRepository->findFeaturedProducts();
        
        if(count($products) > 0){
            if(isset($currency) AND $currency != ""){
                if($currency == "EUR" OR $currency == "USD")
                    $products = $this->productService->getProductConverted($products, $currency);
                else
                    return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
            }
            
            return $this->handleView($this->view($products));
        }
        
        return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
    }
}