<?php

namespace App\Services\HrEdu;

class HrEduUser implements \ArrayAccess
{
    private $container = array();

    private $attributes = [
        "uid", "givenname", "sn", "cn", "userpassword",
        "hrEduPersonUniqueID", "hrEduPersonAffiliation", "hrEduPersonPrimaryAffiliation",
        "hrEduPersonExpireDate", "hrEduPersonHomeOrg", "hrEduPersonPersistentID", "hrEduPersonUniqueNumber",
        'hrEduPersonGender', 'hrEduPersonRole', 'hrEduPersonOIB',
        "objectClass", "mail", "displayname", "postalcode", "street"
    ];

    private $required = [
        'hrEduPersonAffiliation', 'hrEduPersonExpireDate',
        'hrEduPersonHomeOrg', 'hrEduPersonPersistentID',
        'hrEduPersonPrimaryAffiliation', 'hrEduPersonUniqueID',
        'hrEduPersonUniqueNumber', 'hrEduPersonOIB'
    ];

    public function fromEntry($ldapEntry)
    {
        foreach($this->attributes as $attribute) {
            $attributeLower = strtolower($attribute);
            if (!isset($ldapEntry[$attributeLower]))
                return;

            $value = $ldapEntry[$attributeLower];
            $count = $value['count'];
            unset($value['count']);

            if ($count < 2) {
                $value = $ldapEntry[$attributeLower][0];
            }

            if ($attributeLower == 'userpassword') {
                if (!strpos($value, "SHA")) {
                    $value = HrEduService::hash($value);
                }
            }

            $this->setAttribute($attribute, $value);
        }
        return $this;
    }

    public function setName($name, $surname)
    {
        $this->setAttribute('givenname', $name);
        $this->setAttribute('sn', $surname);
        $this->setAttribute('cn', $name . " " . $surname);
        $this->setAttribute('displayname', $name . " " . $surname);
        return $this;
    }

    public function setUid($uid) {
        return $this->setAttribute('uid', $uid);
    }

    public function setPassword($password)
    {
        $this->setAttribute('userpassword',  HrEduService::hash($password));
        return $this;
    }

    public function setAttribute($attribute, $value) {
        $this->container[$attribute] = $value;
        return $this;
    }

    public function setAttributes($attributes) {
        foreach ($attributes as $key => $value)
            $this->setAttribute($key, $value);
        return $this;
    }

    public function setRequiredFieldsEmpty()
    {
        foreach ($this->attributes as $required) {
            $this->setAttribute($required, "---");
        }
        return $this;
    }

    public function __construct (){
        $this->setRequiredFieldsEmpty();
        //$this->setAttribute('hrEduPersonAllowAccess', 'TRUE');
        $this->setAttribute('hrEduPersonExpireDate', 'NONE');
        $this->container['objectClass'] =  [
            'top',
            /*'person',
            'organizationalPerson',
            'inetOrgPerson',*/
            'hrEduPerson'
        ];
    }

    public static function create() {
        return new self();
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset) {
        return $this->container[$offset] ?? null;
    }

    public function asArray () {
        #$this->container['objectclass'] = implode(",", $this->container['objectclass']);
        return $this->container;
    }

}

