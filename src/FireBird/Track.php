<?php

namespace FireBird;

class Track {
  private $projectId;
  private $apiUrl;
  private $version;
  private $endpoint;
  private $finalAttributes = [];

  public function __construct($projectId, $apiUrl, $version = "v1") {
      if (empty($projectId)) {
          throw new \InvalidArgumentException("Project ID cannot be null or empty.");
      }
      $this->projectId = $projectId;
      $this->version = $version;
      $this->endpoint = "/firebird/telcom/" . $this->version . "/save-user-details";
      $this->apiUrl = rtrim($apiUrl, '/') . $this->endpoint; 
  }

  public function setFirstName($value)
  {
      $this->validateName($value, 'firstName');
      $this->addAttribute('firstName', $value, 'String');
  }

  public function setLastName($value)
  {
      $this->validateName($value, 'lastName');
      $this->addAttribute('lastName', $value, 'String');
  }

  public function setUsername($value)
  {
      $this->validateUsername($value);
      $this->addAttribute('username', $value, 'String');
  }

  public function setEmail($value)
  {
      $this->validateEmail($value);
      $this->addAttribute('email', $value, 'String');
  }

  public function setMobileNumber($value)
  {
      $this->validateMobileNumber($value);
      $this->addAttribute('mobileNumber', $value, 'String');
  }

  public function setGender($value)
  {
      $this->addAttribute('gender', $value, 'String');
  }

  public function setBirthDate($value)
  {
      $this->addAttribute('birthDate', $value, 'String');
  }

  public function setAddress($value)
  {
      $this->addAttribute('address', $value, 'String');
  }

  public function setWhatsappNumber($value)
  {
      $this->addAttribute('whatsappNumber', $value, 'String');
  }

  public function setLocation($value)
  {
      $this->addAttribute('location', $value, 'String');
  }

  public function setCity($value)
  {
      $this->addAttribute('city', $value, 'String');
  }

  public function setState($value)
  {
      $this->addAttribute('state', $value, 'String');
  }

  public function setDistrict($value)
  {
      $this->addAttribute('district', $value, 'String');
  }

  public function setUserAttribute($key, $value, $dataType)
  {
      $this->addAttribute($key, $value, $dataType);
  }

  // Set data type by method
  private function supportedDataType($dataType)
  {
      switch (strtolower($dataType)) {
          case 'string':
              return "String";
          case 'integer':
          case 'int':
              return "int";
          case 'float':
          case 'double':
              return "double";
          case 'boolean':
          case 'bool':
              return "boolean";
          case 'array':
              return "array";
          
          default:
              throw new \InvalidArgumentException("Invalid data type provided: $dataType");
      }
  }
  private function addAttribute($key, $value, $dataType)
  {
      $this->finalAttributes[] = [
          'paramName' => $key,
          'paramValue' => $value,
          'paramDatatype' => $this->supportedDataType($dataType)
      ];
  }

  public function execute()
  {
      if (empty($this->finalAttributes)) {
          throw new \RuntimeException("No user details set. Please provide at least one detail.");
      }

      $headers = [
          'Content-Type: application/json',
          'projectId: ' . trim($this->projectId)
      ];

      // cURL request
      $ch = curl_init($this->apiUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->finalAttributes));
      
      // Debugging output
      print_r($this->finalAttributes);

      $response = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($httpCode !== 200) {
          throw new \RuntimeException("API request failed with status code: $httpCode. Response: $response");
      }

      return json_decode($response, true);
  }

  // Validation methods
  private function validateName($name, $fieldName)
  {
      if (empty($name)) {
          throw new \InvalidArgumentException("$fieldName cannot be empty.");
      }
      if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
          throw new \InvalidArgumentException("$fieldName can only contain letters and spaces.");
      }
  }

  private function validateUsername($username)
  {
      if (empty($username)) {
          throw new \InvalidArgumentException("Username cannot be empty.");
      }
      if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
          throw new \InvalidArgumentException("Username can only contain letters, numbers, and underscores.");
      }
  }

  private function validateEmail($email)
  {
     
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          throw new \InvalidArgumentException("Invalid email format.");
      }
  }

  private function validateMobileNumber($mobileNumber)
  {
     
      if (!preg_match("/^\d{15}$/", $mobileNumber)) {
          throw new \InvalidArgumentException("Mobile number must be 15 digits.");
      }
  }
}