<?php

    class Shortify {

        private $_errors = array();

        private $_validated_url = false;

        private function _validate_url($url)
        {
            // If no http(s) at beginning of URL, add it. Assume http.
            if (strpos($url, 'http') !== 0)
            {
                $url = "http://" . $url;
            }

            // Validate URL
            if (filter_var($url, FILTER_VALIDATE_URL) == $url)
                return $url;

            return false;
        }

        private function _check_exists($url)
        {
            $select = $this->_database->prepare('SELECT hash FROM urls WHERE original_url = ? LIMIT 1');

            $select->bind_param('s', $url);
            $select->execute();
            $result = $select->get_result();
            $result_data = $result->fetch_assoc();

            if ($result->num_rows == 0 || (!empty($result_data) && current($result_data) == ""))
                return false;

            return current($result_data);
        }

        private function _create_hash($url)
        {
            $insert = $this->_database->prepare(
                'INSERT INTO urls
                (original_url) VALUES (?)'
            );

            $insert->bind_param('s', $this->_validated_url);
            $result = $insert->execute();

            // Get the insert id
            $insert_id = $this->_database->insert_id;

            $set_hash = $this->_database->prepare(
                'UPDATE urls SET hash=? WHERE id=? LIMIT 1'
            );

            $set_hash->bind_param('si', $hash = base64_encode($insert_id), $insert_id);

            $set_hash->execute();

            return $hash;
        }




        public function __construct($url = false)
        {
            $db_config = require_once('config/db.php');
            $this->_database = new mysqli(
                $db_config['server'],
                $db_config['username'],
                $db_config['password'],
                $db_config['db']
            );

            if ($url !== false)
            {
                $validated_url = $this->_validate_url($url);

                // Check if the URL passed the validation
                if ($validated_url !== FALSE)
                {
                    $this->_validated_url = $validated_url;
                }
                else
                {
                    $this->_errors[] = "Invalid URL";
                }
            }
        }

        public static function init($url)
        {
            self::__construct($url);
        }

        public function errors()
        {
            return $this->_errors;
        }



        public function get_hash()
        {
            if (!empty($this->_errors) || $this->_validated_url === false)
            {
                $this->_errors[] = 'Error with provided URL';
                return false;
            }

            // Check URL already exists in DB
            if ($existing_hash = $this->_check_exists($this->_validated_url))
                return $existing_hash;

            // Insert URL into the database and create a hash
            return $this->_create_hash($this->_validated_url);
        }

        public function get_original_url($hash)
        {
            if (empty($hash))
            {
                $this->_errors[] = "Hash is invalid";
                return false;
            }

            $select = $this->_database->prepare('SELECT original_url FROM urls WHERE hash=? LIMIT 1');
            $select->bind_param('s', $hash);
            $select->execute();
            $result = $select->get_result();
            $result_data = $result->fetch_assoc();

            if ($result->num_rows == 0 || (!empty($result_data) && current($result_data) == ""))
                return false;

            return $result_data['original_url'];
        }










    }




?>
