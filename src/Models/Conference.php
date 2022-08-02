<?php

namespace Models;

use Core\Model;
use Models\Country;
use DateTime;

class Conference extends Model
{
    /**
     * Creates new conference
     * returns true if conference has been added and false if not
     */
    public function addConference($title, $conductDate, $countryId, $lat, $lng)
    {
        $sql = 'INSERT INTO conference (title, conductDate, countryId, lat, lng) VALUES (:title, :conductDate, :countryId, :lat, :lng)';
        $addressIsNotEmpty = Conference::addressIsNotEmpty($lat, $lng);

        $this->db->query($sql);
        $this->db->bind(":title", $title);
        $this->db->bind(":conductDate", $conductDate);
        $this->db->bind(":countryId", $countryId);
        $this->db->bind(":lat", $addressIsNotEmpty ? $lat : null);
        $this->db->bind(":lng", $addressIsNotEmpty ? $lng : null);
        $this->db->execute();
        // rowCount = 0 means no rows have been added
        return $this->db->rowCount() > 0 ? True : False;
    }

    public function getConferenceById($id, $includeCountry = false)
    {
        // if $id isn't number there is no need to send query to database
        if (!is_numeric($id)) {
            return false;
        }

        if (!$includeCountry) {
            $sql = 'SELECT * FROM conference WHERE id=:id LIMIT 1';
        } else {
            // if country has to be included the query contains countryTitle
            $sql = 'SELECT conf.id, conf.title, conf.conductDate, conf.countryId, cntr.title as "countryTitle", conf.lat, conf.lng
            FROM conference conf
            JOIN country cntr ON(conf.countryId=cntr.id)
            WHERE conf.id=:id
            LIMIT 1';
        }

        $this->db->query($sql);
        $this->db->bind(":id", $id);
        $conference = $this->db->fetch();

        return $conference;
    }

    public function getConferencies($includeCountry = false)
    {
        if (!$includeCountry) {
            $sql = 'SELECT * FROM conference ORDER BY conductDate';
        } else {
            // if country has to be included the query contains countryTitle
            $sql = 'SELECT conf.id, conf.title, conf.countryId, cntr.title as "countryTitle", conf.lat, conf.lng
            FROM conference conf
            JOIN country cntr ON(conf.countryId=cntr.id) 
            ORDER BY conf.conductDate';
        }

        $this->db->query($sql);
        $conferencies = $this->db->fetchAll();

        return $conferencies;
    }

    public function updateConference($id, $title,  $conductDate, $countryId, $lat, $lng)
    {
        // if $id isn't number there is no need to send query to database
        if (!is_numeric($id)) {
            return false;
        }

        $sql = 'UPDATE conference SET title=:title, conductDate=:conductDate, countryId=:countryId, lat=:lat, lng=:lng WHERE id=:id';
        $addressIsNotEmpty = Conference::addressIsNotEmpty($lat, $lng);

        $this->db->query($sql);
        $this->db->bind(":id", $id);
        $this->db->bind(":title", $title);
        $this->db->bind(":conductDate", $conductDate);
        $this->db->bind(":countryId", $countryId);
        $this->db->bind(":lat", $addressIsNotEmpty ? $lat : null);
        $this->db->bind(":lng", $addressIsNotEmpty ? $lng : null);

        $this->db->execute();
        // rowCount = 0 means no rows have been updated
        return $this->db->rowCount() > 0 ? True : False;
    }

    public function deleteConference($id)
    {
        // if $id isn't number there is no need to send query to database
        if (!is_numeric($id)) {
            return false;
        }

        $sql = 'DELETE FROM conference WHERE id=:id';

        $this->db->query($sql);
        $this->db->bind(":id", $id);
        $this->db->execute();
        // rowCount = 0 means no rows have been deleted
        return $this->db->rowCount() > 0 ? True : False;
    }

    public function getErrorsFromValidation(&$title, $conductDate, $countryId, $lat, $lng, $validateCountry = true)
    {
        $errors = [];

        $title_len = mb_strlen($title);
        if (!$title) {
            $errors['title'] = "Обязательное поле";
        } else if ($title_len < 2 or $title_len > 255) {
            $errors['title'] = "Название должно быть длиной от 2 до 255 символов";
        }

        if (!$conductDate) {
            $errors['conductDate'] = "Обязательное поле";
        } else {
            $d = DateTime::createFromFormat('Y-m-d', $conductDate);
            $dateErrors = DateTime::getLastErrors();
            // Date is valid if only it in the format of 'Y-m-d' like '2022-08-01' and not less than today
            if ((!$d) || (!$d->format('Y-m-d') === $conductDate) || (!empty($dateErrors['warning_count']))) {
                $errors['conductDate'] = "Дата указана в неверном формате";
            } else if ($conductDate < date('Y-m-d')) {
                $errors['conductDate'] = "Вы не можете указать прошедшую дату";
            }
        }

        if (!$countryId) {
            $errors['countryId'] = "Обязательное поле";
        } else if ($validateCountry) {
            $CountryModel = Conference::getCountryModel();

            if (!$CountryModel->existsById($countryId)) {
                $errors['countryId'] = "Страна указана неверно";
            }
        }

        $addressIsNotEmpty = Conference::addressIsNotEmpty($lat, $lng);
        // if address is null must not validate it
        if ($addressIsNotEmpty) {
            $lat_float = (float)$lat;
            // latitude has to be between -90 and 90
            if ((!is_numeric($lat)) or $lat_float < -90 or $lat_float > 90) {
                $errors['lat'] = "Неверно указана широта";
            }
        }

        if ($addressIsNotEmpty) {
            $lng_float = (float)$lng;
            // longitude has to be between -180 and 180
            if ((!is_numeric($lng)) or $lng_float < -180 or $lng_float > 180) {
                $errors['lng'] = "Неверно указана долгота";
            }
        }

        return $errors;
    }

    public function addressIsSet($lat, $lng)
    {
        if (!Conference::addressIsNotEmpty($lat, $lng)) {
            return false;
        }

        if ((!is_numeric($lat)) or (!is_numeric($lng))) {
            return false;
        }

        $lat_float = (float)$lat;
        if ($lat_float < -90 or $lat_float > 90) {
            return false;
        }

        $lng_float = (float)$lng;
        if ($lng_float < -180 or $lng_float > 180) {
            return false;
        }

        return true;
    }

    public function addressIsNotEmpty($lat, $lng)
    {
        // value 0 is valid
        return !($lat === "") and !is_null($lat) and !($lng === "") and !is_null($lng);
    }

    public function getCountryModel()
    {
        return new Country();
    }
}
