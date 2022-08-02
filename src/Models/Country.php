<?php

namespace Models;

use Core\Model;
use PDO;

class Country extends Model
{
    public function getCountries()
    {
        $sql = 'SELECT id, title FROM country ORDER BY title';

        $this->db->query($sql);
        $countries = $this->db->fetchAll();

        return $countries;
    }

    public function getCountryById($id)
    {
        // if $id isn't number there is no need to send query to database
        if (!is_numeric($id)) {
            return false;
        }

        $sql = 'SELECT id, title FROM country WHERE id=:id LIMIT 1';

        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $country = $this->db->fetch();

        return $country;
    }

    public function existsById($id)
    {
        // if $id isn't number there is no need to send query to database
        if (!is_numeric($id)) {
            return false;
        }

        $sql = 'SELECT EXISTS (SELECT * FROM country WHERE id=:id)';

        $this->db->query($sql);
        $this->db->bind(':id', $id);
        $exists = $this->db->fetch(PDO::FETCH_COLUMN);

        return $exists;
    }
}
