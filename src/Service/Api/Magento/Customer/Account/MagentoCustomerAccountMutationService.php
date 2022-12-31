<?php

namespace App\Service\Api\Magento\Customer\Account;

use App\DataClass\Customer\CustomerAccount;
use App\Service\Api\Magento\BaseMagentoService;
use App\Service\GraphQL\Field;
use App\Service\GraphQL\Input;
use App\Service\GraphQL\InputField;
use App\Service\GraphQL\Mutation;
use App\Service\GraphQL\Parameter;
use App\Service\GraphQL\Request;

class MagentoCustomerAccountMutationService extends BaseMagentoService
{
    public function generateCustomerToken(CustomerAccount $customerAccount): false|array
    {
        $response = (new Request(
            (new Mutation('generateCustomerToken')
            )->addParameter(
                new Parameter('email', $customerAccount->getEmail())
            )->addParameter(
                new Parameter('password', $customerAccount->getPassword())
            )->addField(
                new Field('token')
            ),
            $this->mageGraphQlClient
        ))->send();

        if ( ! array_key_exists('errors', $response)) {
            $session = $this->requestStack->getSession();
            $session->set('customerToken', $response['data']['generateCustomerToken']['token']);

            return false;
        }

        return $response;
    }

    public function revokeCustomerToken(): bool
    {
        $response = (new Request(
            (new Mutation('revokeCustomerToken')
            )->addField(
                new Field('result')
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['data']['revokeCustomerToken']['result'] ?? false;
    }

    public function createCustomer(CustomerAccount $customerAccount): false|array
    {
        $response = (new Request(
            (new Mutation('createCustomer')
            )->addParameter(
                (new Input('input')
                )->addFields(
                    [
                        new InputField('firstname', $customerAccount->getFirstname()),
                        new InputField('lastname', $customerAccount->getLastname()),
                        new InputField('email', $customerAccount->getEmail()),
                        new InputField('password', $customerAccount->getPassword()),
                        new InputField('is_subscribed', (bool)$customerAccount->getIsSubscribed()),
                    ]
                ),
            )->addField(
                (new Field('customer')
                )->addChildFields(
                    [
                        new Field('firstname'),
                        new Field('lastname'),
                        new Field('email'),
                        new Field('is_subscribed'),
                    ]
                )
            ),
            $this->mageGraphQlClient,
        ))->send();

        return $response['errors'] ?? false;
    }

    public function requestPasswordResetEmail(string $email): false|array
    {
        $response = (new Request(
            (new Mutation('requestPasswordResetEmail')
            )->addParameter(
                new Parameter('email', $email)
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['errors'] ?? false;
    }

    public function resetPassword(string $email): false|array
    {
        $response = (new Request(
            (new Mutation('resetPassword')
            )->addParameter(
                new Parameter('email', $email)
            ),
            $this->mageGraphQlClient
        ))->send();

        return $response['errors'] ?? false;
    }
}