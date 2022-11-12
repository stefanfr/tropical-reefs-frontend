<?php

namespace App\DataClass\Checkout\Address;

class Address
{
    protected ?string $customerEmail = 'stefanfransen1@hotmail.com';

    protected ?string $firstname = 'Stefan';

    protected ?string $lastname = 'Fransen';

    protected ?string $company = 'Tropical Reefs';

    protected ?string $phone = '06123456789';

    protected ?string $countryCode = 'NL';

    protected ?string $postcode = '5051SP';

    protected ?int $houseNr = 8;

    protected ?string $add = null;

    protected ?string $street = null;

    protected ?string $city = null;

    /**
     * @return string|null
     */
    public function getCustomerEmail(): ?string
    {
        return $this->customerEmail;
    }

    /**
     * @param string|null $customerEmail
     * @return $this
     */
    public function setCustomerEmail(?string $customerEmail): Address
    {
        $this->customerEmail = $customerEmail;

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
     * @return Address
     */
    public function setFirstname(?string $firstname): Address
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
     * @return Address
     */
    public function setLastname(?string $lastname): Address
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
     * @return Address
     */
    public function setCompany(?string $company): Address
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return Address
     */
    public function setPhone(?string $phone): Address
    {
        $this->phone = $phone;

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
     * @return $this
     */
    public function setCountryCode(?string $countryCode): Address
    {
        $this->countryCode = $countryCode;

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
     * @return Address
     */
    public function setPostcode(?string $postcode): Address
    {
        $this->postcode = $postcode;

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
     * @return Address
     */
    public function setHouseNr(?int $houseNr): Address
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
     * @return Address
     */
    public function setAdd(?string $add): Address
    {
        $this->add = $add;

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
     * @return Address
     */
    public function setStreet(?string $street): Address
    {
        $this->street = $street;

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
     * @return Address
     */
    public function setCity(?string $city): Address
    {
        $this->city = $city;

        return $this;
    }
}