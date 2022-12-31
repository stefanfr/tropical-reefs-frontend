<?php

namespace App\DataClass\Customer;

class CustomerAccount
{
    protected ?string $email = null;
    protected ?string $password = null;
    protected ?string $newPassword = null;
    protected ?string $firstname = null;
    protected ?string $lastname = null;
    protected ?string $isSubscribed = null;

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return CustomerAccount
     */
    public function setEmail(string $email): CustomerAccount
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return CustomerAccount
     */
    public function setPassword(string $password): CustomerAccount
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param string|null $newPassword
     * @return CustomerAccount
     */
    public function setNewPassword(?string $newPassword): CustomerAccount
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return CustomerAccount
     */
    public function setFirstname(string $firstname): CustomerAccount
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return CustomerAccount
     */
    public function setLastname(string $lastname): CustomerAccount
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return string
     */
    public function getIsSubscribed(): string
    {
        return $this->isSubscribed;
    }

    /**
     * @param string $isSubscribed
     * @return CustomerAccount
     */
    public function setIsSubscribed(string $isSubscribed): CustomerAccount
    {
        $this->isSubscribed = $isSubscribed;

        return $this;
    }
}