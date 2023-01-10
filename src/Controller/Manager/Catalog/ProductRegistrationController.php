<?php

namespace App\Controller\Manager\Catalog;

use App\Entity\Manager\Product;
use App\Repository\Manager\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductRegistrationController extends AbstractController
{
    public function __construct(
        protected ManagerRegistry   $doctrine,
        protected ProductRepository $productRepository,
    )
    {
    }

    #[Route('/manager/catalog/product/registration', name: 'app_manager_catalog_product_registration')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->render('manager/catalog/product_registration/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/manager/catalog/product/registration/edit/{product}', name: 'app_manager_catalog_product_registration_edit')]
    public function edit(Request $request, Product $product)
    {
        $files = $request->files->all();

        return $this->render('manager/catalog/product_registration/edit.html.twig');
    }

    #[Route('/manager/catalog/product/registration/update', name: 'app_manager_catalog_product_registration_update', methods: ['PUT'])]
    public function update(Request $request)
    {
        $files = $request->files->all();

        dd($files);
    }

    /**
     * @throws InvalidArgument
     * @throws Exception
     */
    #[Route('/manager/catalog/product/registration/upload', name: 'app_manager_catalog_product_registration_upload', methods: ['POST'])]
    public function upload(Request $request)
    {
        /** @var UploadedFile $csv */
        $csv = $request->files->get('uploadCsv');
        $em = $this->doctrine->getManager('manager');

        $csv = Reader::createFromString($csv->getContent());
        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();
        $csv->chunk(1);
        $index = 1;
        foreach ($csv->getRecords($headers) as $record) {
            $oldProductData = $this->productRepository->findOneBy(
                [
                    'supplierCode' => $record['supplier_code'],
                ]
            );

            if (null === $oldProductData) {
                $lastSku = $this->productRepository->findBy(
                    [
                        'brand' => ucfirst(strtolower($record['category'])),
                    ]
                );

                $newSku = str_pad((string)$index, 4, '0', STR_PAD_LEFT);
                $index++;
                $product = new Product();
                $product->setSupplierCode($record['supplier_code'])
                    ->setBrand(ucfirst(strtolower($record['category'])))
                    ->setSku(
                        'AH-' . $newSku
                    )
                    ->setProductData($record);

                $em->persist($product);
                continue;
            }
            $oldProductData->setProductData($record);
        }

        $em->flush();

        return $this->redirectToRoute('app_manager_catalog_product_registration');
    }

    #[Route('/manager/catalog/product/registration/update', name: 'app_manager_catalog_product_registration_update', methods: ['POST'])]
    public function save(Request $request)
    {
        $files = $request->files->all();

        dd($files);
    }

    #[Route('/manager/catalog/product/registration/delete', name: 'app_manager_catalog_product_registration_delete')]
    public function delete(Request $request)
    {
        $files = $request->files->all();

        dd($files);
    }
}
