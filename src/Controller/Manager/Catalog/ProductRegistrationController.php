<?php

namespace App\Controller\Manager\Catalog;

use App\Entity\Manager\Product;
use App\Repository\Manager\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $products = $this->productRepository->findAll();

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

        return $this->render('manager/catalog/product_registration/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/manager/catalog/product/registration/edit/{product}', name: 'app_manager_catalog_product_registration_edit')]
    public function edit(Request $request, Product $product): Response
    {
        return $this->render('manager/catalog/product_registration/edit.html.twig', [
            'product' => $product,
        ]);
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
    public function upload(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        /** @var UploadedFile $csv */
        $csv = $request->files->get('uploadCsv');
        $em = $this->doctrine->getManager('manager');

        $csv = Reader::createFromString($csv->getContent());
        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);
        $headers = $csv->getHeader();
        $csv->chunk(1);
        foreach ($csv->getRecords($headers) as $record) {
            $oldProductData = $this->productRepository->findOneBy(
                [
                    'supplierCode' => $record['supplier_code'],
                ]
            );

            $record['name'] = trim(str_replace([ucfirst(strtolower($record['category'])), $record['category'], strtolower($record['category']), strtoupper($record['category'])], '', $record['name']));
            $record['name'] = preg_replace('/(AMA |- |-Futter |-Futter\/Granulat |-Futter\/Pulver |, )/', '', $record['name']);
            $record['name'] = preg_replace("/[\n\r]/", ' ', $record['name']);

            if (null === $oldProductData) {
                $product = new Product();
                $product->setSupplierCode($record['supplier_code'])
                    ->setBrand(ucfirst(strtolower($record['category'])))
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
