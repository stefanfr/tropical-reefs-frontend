<?php

namespace App\Controller\Api\Customer\General;

use App\Controller\Customer\AbstractCustomerController;
use App\Service\Api\Magento\Customer\Account\General\MagentoCustomerAccountGeneralMutationService;
use App\Service\Api\Magento\Customer\Account\MagentoCustomerAccountQueryService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/customer/settings', name: 'app_customer_general')]
class GeneralSettingsController extends AbstractCustomerController
{
    public function __construct(
        protected MagentoCustomerAccountQueryService           $magentoCustomerAccountQueryService,
        protected MagentoCustomerAccountGeneralMutationService $magentoCustomerAccountGeneralMutationService,
    )
    {
        parent::__construct($magentoCustomerAccountQueryService);
    }

    #[Route('/save/general', name: '_save_general', methods: ['POST'])]
    public function saveGeneral(Request $request): JsonResponse
    {
        if (!$this->isAuthenticated($request)) {
            return new JsonResponse(
                'User is not authenticated',
                Response::HTTP_UNAUTHORIZED,
            );
        }

        $response = $this->magentoCustomerAccountGeneralMutationService->saveSettings(
            $request->get('customerData')
        );

        if ($response) {
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/save/password', name: '_save_password', methods: ['POST'])]
    public function savePassword(Request $request): JsonResponse
    {
        if (!$this->isAuthenticated($request)) {
            return new JsonResponse(
                'User is not authenticated',
                Response::HTTP_UNAUTHORIZED,
            );
        }

        $response = $this->magentoCustomerAccountGeneralMutationService->savePassword(
            $request->get('currentPassword'),
            $request->get('newPassword'),
        );

        if ($response) {
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }
}