<?php


namespace App\Traits;


trait SecondaryFieldsSchool
{
    public function getNameAttribute($value)
    {
        return explode(';', $value);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = implode(';', $value);
    }

    public function getPostalAddressAttribute($value)
    {
        return explode(';', $value);
    }

    public function setPostalAddressAttribute($value)
    {
        $this->attributes['postal_address'] = implode(';', $value);
    }

    public function getLocationAttribute($value)
    {
        return explode(';', $value);
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = implode(';', $value);
    }

    public function getPostalNoAttribute($value)
    {
        return explode(';', $value);
    }

    public function setPostalNoAttribute($value)
    {
        $this->attributes['postal_no'] = implode(';', $value);
    }

    public function getAddressAttribute($value)
    {
        return explode(';', $value);
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = implode(';', $value);
    }

    public function getTelephoneAttribute($value)
    {
        return explode(';', $value);
    }

    public function setTelephoneAttribute($value)
    {
        $this->attributes['telephone'] = implode(';', $value);
    }

    public function getFaxAttribute($value)
    {
        return explode(';', $value);
    }

    public function setFaxAttribute($value)
    {
        $this->attributes['fax'] = implode(';', $value);
    }

    public function getMobilePhoneAttribute($value)
    {
        return explode(';', $value);
    }

    public function setMobilePhoneAttribute($value)
    {
        $this->attributes['mobile_phone'] = implode(';', $value);
    }

    public function getEmailAttribute($value)
    {
        return explode(';', $value);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = implode(';', $value);
    }

    public function getAffiliationAttribute($value)
    {
        return explode(';', $value);
    }

    public function setAffiliationAttribute($value)
    {
        $this->attributes['affiliation'] = implode(';', $value);
    }

    public function getUriPolicyAttribute($value)
    {
        return explode(';', $value);
    }

    public function setUriPolicyAttribute($value)
    {
        $this->attributes['uri_policy'] = implode(';', $value);
    }

}
