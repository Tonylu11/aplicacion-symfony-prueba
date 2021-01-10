<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Category;

use App\Form\CategoryType;
use App\Repository\CategoryRepository;

class CategoriesController extends AbstractFOSRestController
{

    /**
     * Crea Categoria.
     * @Rest\Post("/category")
     * 
     * @return Response
    */
    public function createCategoryAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Modificar Categoria.
     * @Rest\Patch("/category/{idCat}")
     * 
     * @return Response
    */
    public function modifyCategoryAction(Request $request, $idCat)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->findOneBy(array("id" => $idCat));

        if(isset($category)){
            $form = $this->createForm(CategoryType::class, $category, array("method" => $request->getMethod()));
            $data = json_decode($request->getContent(), true);
            $form->submit($data, false);
    
            if($form->isSubmitted() && $form->isValid()){
                $em->persist($category);
                $em->flush();
    
                return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_OK));
            }

            return $this->handleView($this->view($form->getErrors()));
        }

        return $this->handleView($this->view(null, Response::HTTP_NOT_FOUND));
    }

    /**
     * Eliminar Categoria.
     * @Rest\Delete("/category/{idCat}")
     * 
     * @return Response
    */
    public function deleteCategoryAction(Request $request, $idCat)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->findOneBy(array("id" => $idCat));

        if(isset($category)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            
            return $this->handleView($this->view(['status'=>'ok'], Response::HTTP_CREATED));
        }

        return $this->handleView($this->view(null, Response::HTTP_NOT_FOUND));
    }
}