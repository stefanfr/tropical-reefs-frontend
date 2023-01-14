<?php

namespace App\Controller\Manager\Catalog;

use App\Entity\Manager\GeneratedProductModel;
use App\Repository\Manager\GeneratedProductModelRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GeneratedProductModelController extends AbstractController
{
    public function __construct(
        protected GeneratedProductModelRepository $generatedProductModelRepository,
    )
    {
    }

    #[Route('/manager/catalog/generated/product-models', name: 'app_manager_catalog_generated_product_models')]
    public function index(PaginatorInterface $paginator, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $products = $this->generatedProductModelRepository->findAll();

        $pagination = $paginator->paginate(
            $products, /* query NOT result */
            $request->query->getInt('page', 1),
            100,
            [
                'sortFieldAllowList' => [
                    'brand',
                    'supplier_code',
                    'sku',
                ],
            ]
        );

        return $this->render('manager/catalog/generated/product_model/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/manager/catalog/generated/product-models/{product}', name: 'app_manager_catalog_generated_product_model_edit')]
    public function edit(GeneratedProductModel $product)
    {

    }

    #[Route('/manager/catalog/generated/product-models/delete/{product}', name: 'app_manager_catalog_generated_product_model_delete')]
    public function delete(GeneratedProductModel $product)
    {

    }
}