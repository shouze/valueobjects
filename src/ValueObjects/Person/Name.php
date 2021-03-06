<?php

namespace ValueObjects\Person;

use ValueObjects\String\String;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;

class Name implements ValueObjectInterface
{
    /**
     * First name
     *
     * @var \ValueObjects\String\String
     */
    private $firstName;

    /**
     * Middle name
     *
     * @var \ValueObjects\String\String
     */
    private $middleName;

    /**
     * Last name
     *
     * @var \ValueObjects\String\String
     */
    private $lastName;

    /**
     * Returns a Name objects form PHP native values
     *
     * @param  string $first_name
     * @param  string $middle_name
     * @param  string $last_name
     * @return Name
     */
    public static function fromNative()
    {
        $args = func_get_args();

        $firstName  = new String($args[0]);
        $middleName = new String($args[1]);
        $lastName   = new String($args[2]);

        return new self($firstName, $middleName, $lastName);
    }

    /**
     * Returns a Name object
     *
     * @param String $first_name
     * @param String $middle_name
     * @param String $last_name
     */
    public function __construct(String $first_name, String $middle_name, String $last_name)
    {
        $this->firstName  = $first_name;
        $this->middleName = $middle_name;
        $this->lastName   = $last_name;
    }

    /**
     * Returns the first name
     *
     * @return String
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Returns the middle name
     *
     * @return String
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Returns the last name
     *
     * @return String
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Returns the full name
     *
     * @return String
     */
    public function getFullName()
    {
        $fullName = new String($this->firstName . ' ' . $this->middleName . ' ' . $this->lastName);

        return $fullName;
    }

    /**
     * Tells whether two names are equal by comparing their values
     *
     * @param  ValueObjectInterface $name
     * @return bool
     */
    public function equals(ValueObjectInterface $name)
    {
        if (false === Util::classEquals($this, $name)) {
            return false;
        }

        return $this->getFullName() == $name->getFullName();
    }

    /**
     * Returns the full name
     *
     * @return string
     */
    public function __toString()
    {
        return strval($this->getFullName());
    }
}
