<?php


namespace App\Traits;


trait SecondaryFieldsUser
{
    public function getFirstNameAttribute($value)
    {
        return explode(';', $value);
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = implode(';', $value);
    }

    public function getLastNameAttribute($value)
    {
        return explode(';', $value);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = implode(';', $value);
    }

    public function getGroupAffiliationAttribute($value)
    {
        return explode(';', $value);
    }

    public function setGroupAffiliationAttribute($value)
    {
        $this->attributes['group_affiliation'] = implode(';', $value);
    }

    public function getPhoneNumberAttribute($value)
    {
        return explode(';', $value);
    }

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = implode(';', $value);
    }

    public function getMobilePhoneNumberAttribute($value)
    {
        return explode(';', $value);
    }

    public function setMobilePhoneNumberAttribute($value)
    {
        $this->attributes['mobile_phone_number'] = implode(';', $value);
    }

    public function getInstitutionRoleAttribute($value)
    {
        return explode(';', $value);
    }

    public function setInstitutionRoleAttribute($value)
    {
        $this->attributes['institution_role'] = implode(';', $value);
    }

    public function getInstitutionJobTypeAttribute($value)
    {
        return explode(';', $value);
    }

    public function setInstitutionJobTypeAttribute($value)
    {
        $this->attributes['institution_job_type'] = implode(';', $value);
    }

    public function getOrganisationalUnitAttribute($value)
    {
        return explode(';', $value);
    }

    public function setOrganisationalUnitAttribute($value)
    {
        $this->attributes['organisational_unit'] = implode(';', $value);
    }

    public function getRoomNumberAttribute($value)
    {
        return explode(';', $value);
    }

    public function setRoomNumberAttribute($value)
    {
        $this->attributes['room_number'] = implode(';', $value);
    }

    public function getStreetHouseNumberAttribute($value)
    {
        return explode(';', $value);
    }

    public function setStreetHouseNumberAttribute($value)
    {
        $this->attributes['street_house_number'] = implode(';', $value);
    }

    public function getHomePostalAddressAttribute($value)
    {
        return explode(';', $value);
    }

    public function setHomePostalAddressAttribute($value)
    {
        $this->attributes['home_postal_address'] = implode(';', $value);
    }

    public function getHomePhoneNumberAttribute($value)
    {
        return explode(';', $value);
    }

    public function setHomePhoneNumberAttribute($value)
    {
        $this->attributes['home_phone_number'] = implode(';', $value);
    }

    public function getDesktopDeviceAttribute($value)
    {
        return explode(';', $value);
    }

    public function setDesktopDeviceAttribute($value)
    {
        $this->attributes['desktop_device'] = implode(';', $value);
    }

    public function getPrivacyLabelAttribute($value)
    {
        return explode(';', $value);
    }

    public function setPrivacyLabelAttribute($value)
    {
        $this->attributes['privacy_label'] = implode(';', $value);
    }

}
