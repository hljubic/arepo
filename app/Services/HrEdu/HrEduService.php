<?php
namespace App\Services\HrEdu;

class HrEduService {

    private $dnBase;
    private $ldapConnection;

    public function __construct()
    {
        $this->dnBase = env("LDAP_DN_BASE");
        $this->ldapConnection =  self::connect();

        // Branch
        // $this->dnBase = 'dc=skole,' . $this->dnBase;
    }

    public function add($user) {
        $ldapDn = "uid=" . $user['uid'] . "," . $this->dnBase;
        return ldap_add($this->ldapConnection, $ldapDn, $user->asArray());
    }

    public function get($uid) {
        $filter = "(uid=$uid)";
        $result = ldap_search($this->ldapConnection, $this->dnBase, $filter) or abort(481, 'Došlo je do pogreške u LDAP-u!');
        $entries = ldap_get_entries($this->ldapConnection, $result);

        if (!$entries['count'] > 0)
            throw new \Exception("AAI user does not exist.", 404);

        $entry = $entries[0];

        $user = new HrEduUser();
        try {
            $user->fromEntry($entry);
        } catch (\Exception $e) {
            $user = new HrEduUser();
        }

        return $user;
    }

    public function edit($object, $uid) {
        $ldapDn = "uid=" . $uid . "," . $this->dnBase;
        if ($object['uid'] != $uid) {
            $newLdapDn = "uid=" . $object['uid'];
            ldap_rename($this->ldapConnection, $ldapDn, $newLdapDn, $this->dnBase, true);
            $ldapDn = $newLdapDn. ",". $this->dnBase;
        }

        return ldap_modify($this->ldapConnection, $ldapDn, $object);
    }

    public  function delete($uid) {
        $ldapDn = "uid=" . $uid . "," . $this->dnBase;
        return ldap_delete($this->ldapConnection, $ldapDn);
    }

    public function all() {
        $filter = "(objectclass=hrEduPerson)";
        @ldap_control_paged_result($this->ldapConnection, 100);
        $result = ldap_search($this->ldapConnection, $this->dnBase, $filter) or abort(481, 'Došlo je do pogreške u LDAP-u!');
        $entries = ldap_get_entries($this->ldapConnection, $result);

        $users = array();

        foreach ($entries as $entry) {
            if (is_array($entry)) {
                $user = new HrEduUser();
                try {
                    //dd($entry);
                    $user->fromEntry($entry);
                } catch (\Exception $e) {
                    $user = new HrEduUser();
                }
                array_push($users, $user);
            }
        }

        return array_reverse($users);
    }

    public function search($query) {
        $filter = "(&(objectclass=sumEduPerson)(|(sumEduPersonUniqueNumber=*$query)(cn=*$query*)(sumEduPersonHomeOrg=*$query*)(sumEduPersonRole=$query*)))";

        return $this->filter($filter);
    }

    public function filter($filter) {

        @ldap_control_paged_result($this->ldapConnection, 1000);

        $result = ldap_search($this->ldapConnection, $this->dnBase, $filter) or abort(481, 'Došlo je do pogreške u LDAP-u!');
        $entries = ldap_get_entries($this->ldapConnection, $result);

        $users = array();

        foreach ($entries as $entry) {
            if (is_array($entry)) {
                array_push($users, self::get($entry['uid'][0]));
            }
        }

        return array_reverse($users);
    }

    public function exists($uid)
    {
        $filter = "(uid=$uid)";
        $result = ldap_search($this->ldapConnection, $this->dnBase, $filter) or abort(481, 'Došlo je do pogreške u LDAP-u!');
        $entries = ldap_get_entries($this->ldapConnection, $result);
        return $entries['count'] > 0;
    }

    public function auth($username, $password)
    {
        if (!strpos($password, 'SHA') && $password != "") {
            $password = self::hash($password);
        }

        $username = ldap_escape($username, "", LDAP_ESCAPE_FILTER);

        $filter = "(&(uid=" . $username . ")(userPassword=" . $password . "))";
        $result = ldap_search($this->ldapConnection, $this->dnBase, $filter) or exit("Unable to search LDAP server");
        $entries = ldap_get_entries($this->ldapConnection, $result);
        return $entries['count'] != 0;
    }

    public static function hash($password) {
        return "{SHA}" . base64_encode(pack("H*", sha1($password)));
    }

    public function closeConnection()
    {
        ldap_close($this->ldapConnection);
    }

    // Branch
    /*public static function getUserBranch($user)
    {
        $branch = 'sum.ba';
        if (static::isSkoleBranch($user)) {
            $branch = 'skole.' . $branch;
        }
        return $branch;
    }

    public static function isSkoleBranch($user)
    {
        return $user['sumEduPersonHomeOrg'] == 'skole.sum.ba';
    }*/


    public static function generateUsername($user, $i = 0)
    {
        $characters = ['ć', 'č', 'š', 'đ', 'ž', ' ', '-'];
        $replacements = ['c', 'c', 's', 'd', 'z', '.', '.'];

        $ime = HrEduService::formatName($user->first_name[0]);
        $prezime = HrEduService::formatName($user->last_name[0]);

        $ime = str_replace($characters, $replacements, mb_strtolower($ime));
        $prezime = str_replace($characters, $replacements, mb_strtolower($prezime));

        $username = $ime . '.' . $prezime;

        if ($i != 0) {
            $username .= $i;
        }

        return $username;
    }

    public static function formatName($name) {
        $name = ucfirst(mb_strtolower($name));
        $delimiters = ['-', ' '];
        foreach ($delimiters as $delimiter) {
            if (strpos($name, $delimiter) !== false) {
                $items = explode($delimiter, $name);
                foreach ($items as &$item) {
                    $item = ucfirst(mb_strtolower($item));
                }
                $name = implode($delimiter, $items);
            }
        }
        return $name;
    }

    public static function connect () {
        $ldapDn = 'cn=' . env('LDAP_USERNAME') . ',' . env('LDAP_DN_BASE');
        $ldapPassword = env('LDAP_PASSWORD');

        $ldapConnection = ldap_connect(env('LDAP_HOST'), $port=389);

        ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (ldap_bind($ldapConnection, $ldapDn, $ldapPassword)) {
            return $ldapConnection;
        } else {
            throw new \Exception("LDAP autorizacija nije uspjela molim pokušajte druge podatke.");
            //return false;
        }
    }

}
