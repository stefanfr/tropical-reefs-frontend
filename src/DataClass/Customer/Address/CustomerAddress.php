<?php

namespace App\DataClass\Customer\Address;

class CustomerAddress
{
    protected ?int $id = null;
    protected ?string $firstname = null;
    protected ?string $lastname = null;
    protected ?string $company = null;
    protected ?string $street = null;
    protected ?int $houseNr = null;
    protected ?string $add = null;
    protected ?string $postcode = null;
    protected ?string $city = null;
    protected ?string $countryCode = null;
    protected ?string $telephone = null;
    protected bool $isDefaultShipping = true;
    protected bool $isDefaultBilling = true;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return CustomerAddress
     */
    public function setId(?int $id): CustomerAddress
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return CustomerAddress
     */
    public function setFirstname(?string $firstname): CustomerAddress
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return CustomerAddress
     */
    public function setLastname(?string $lastname): CustomerAddress
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     * @return CustomerAddress
     */
    public function setCompany(?string $company): CustomerAddress
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     * @return CustomerAddress
     */
    public function setStreet(?string $street): CustomerAddress
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHouseNr(): ?int
    {
        return $this->houseNr;
    }

    /**
     * @param int|null $houseNr
     * @return CustomerAddress
     */
    public function setHouseNr(?int $houseNr): CustomerAddress
    {
        $this->houseNr = $houseNr;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdd(): ?string
    {
        return $this->add;
    }

    /**
     * @param string|null $add
     * @return CustomerAddress
     */
    public function setAdd(?string $add): CustomerAddress
    {
        $this->add = $add;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    /**
     * @param string|null $postcode
     * @return CustomerAddress
     */
    public function setPostcode(?string $postcode): CustomerAddress
    {
        $this->postcode = $postcode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     * @return CustomerAddress
     */
    public function setCity(?string $city): CustomerAddress
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     * @return CustomerAddress
     */
    public function setCountryCode(?string $countryCode): CustomerAddress
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string|null $telephone
     * @return CustomerAddress
     */
    public function setTelephone(?string $telephone): CustomerAddress
    {
        $this->telephone = $telephone;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultShipping(): bool
    {
        return $this->isDefaultShipping;
    }

    /**
     * @param bool $isDefaultShipping
     * @return CustomerAddress
     */
    public function setIsDefaultShipping(bool $isDefaultShipping): CustomerAddress
    {
        $this->isDefaultShipping = $isDefaultShipping;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDefaultBilling(): bool
    {
        return $this->isDefaultBilling;
    }

    /**
     * @param bool $isDefaultBilling
     * @return CustomerAddress
     */
    public function setIsDefaultBilling(bool $isDefaultBilling): CustomerAddress
    {
        $this->isDefaultBilling = $isDefaultBilling;
        return $this;
    }

}