<?php

namespace App\Controller\Manager\Catalog;

use App\Entity\Manager\GeneratedProduct;
use App\Form\Manager\GeneratedProductType;
use App\Repository\Manager\GeneratedProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GeneratedProductController extends AbstractController
{
    public function __construct(
        protected GeneratedProductRepository $generatedProductRepository,
        protected ManagerRegistry            $managerRegistry,
    )
    {
    }

    #[Route('/manager/catalog/generated/products', name: 'app_manager_catalog_generated_products')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $products = $this->generatedProductRepository->findAll();

        $pagination = $paginator->paginate(
            $products,
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

        return $this->render('manager/catalog/generated/product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/manager/catalog/generated/product/{product}', name: 'app_manager_catalog_generated_product_edit')]
    public function edit(Request $request, GeneratedProduct $product): RedirectResponse|Response
    {
        $form = $this->createForm(GeneratedProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager('manager');
            $em->flush();

            return $this->redirectToRoute('app_manager_catalog_generated_products');
        }

        return $this->render('manager/catalog/generated/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/manager/catalog/generated/product/delete/{product}', name: 'app_manager_catalog_generated_product_delete')]
    public function delete(GeneratedProduct $product): RedirectResponse
    {
        $em = $this->managerRegistry->getManager('manager');
        $em->remove($product);

        return $this->redirectToRoute('app_manager_catalog_generated_products');
    }
}