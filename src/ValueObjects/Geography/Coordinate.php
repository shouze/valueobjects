<?php

namespace ValueObjects\Geography;

use League\Geotools\Convert\Convert;
use League\Geotools\Distance\Distance;
use ValueObjects\Number\Real;
use ValueObjects\String\String;
use ValueObjects\Util\Util;
use ValueObjects\ValueObjectInterface;
use League\Geotools\Coordinate\Coordinate as BaseCoordinate;
use League\Geotools\Coordinate\Ellipsoid as BaseEllipsoid;

class Coordinate implements ValueObjectInterface
{
    /** @var Latitude */
    protected $latitude;

    /** @var Longitude */
    protected $longitude;

    /** @var Ellipsoid */
    protected $ellipsoid;

    /**
     * Returns a new Coordinate object from native PHP arguments
     *
     * @return self
     * @throws \BadMethodCallException
     */
    public static function fromNative()
    {
        $args = \func_get_args();

        if (\count($args) < 2 || \count($args) > 3) {
            throw new \BadMethodCallException('You must provide 2 to 3 arguments: 1) latitude, 2) longitude, 3) valid ellipsoid type (optional)');
        }

        $coordinate = new BaseCoordinate(array($args[0], $args[1]));
        $latitude  = Latitude::fromNative($coordinate->getLatitude());
        $longitude = Longitude::fromNative($coordinate->getLongitude());

        $nativeEllipsoid = isset($args[2]) ? $args[2] : null;
        $ellipsoid = Ellipsoid::fromNative($nativeEllipsoid);

        return new self($latitude, $longitude, $ellipsoid);
    }

    /**
     * Returns a new Coordinate object
     *
     * @param Latitude  $latitude
     * @param Longitude $longitude
     * @param Ellipsoid $ellipsoid
     */
    public function __construct(Latitude $latitude, Longitude $longitude, Ellipsoid $ellipsoid = null)
    {
        if (null === $ellipsoid) {
            $ellipsoid = Ellipsoid::WGS84();
        }

        $this->latitude   = $latitude;
        $this->longitude  = $longitude;
        $this->ellipsoid  = $ellipsoid;
    }

    /**
     * Tells whether tow Coordinate objects are equal
     *
     * @param  ValueObjectInterface $coordinate
     * @return bool
     */
    public function equals(ValueObjectInterface $coordinate)
    {
        if (false === Util::classEquals($this, $coordinate)) {
            return false;
        }

        return $this->getLatitude()->equals($coordinate->getLatitude())   &&
               $this->getLongitude()->equals($coordinate->getLongitude()) &&
               $this->getEllipsoid()->equals($coordinate->getEllipsoid())
        ;
    }

    /**
     * Returns latitude
     *
     * @return Latitude
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Returns longitude
     *
     * @return Longitude
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Returns ellipsoid
     *
     * @return Ellipsoid
     */
    public function getEllipsoid()
    {
        return $this->ellipsoid;
    }

    /**
     * Returns a degrees/minutes/seconds representation of the coordinate
     *
     * @return String
     */
    public function toDegreesMinutesSeconds()
    {
        $coordinate = self::getBaseCoordinate($this);
        $convert    = new Convert($coordinate);
        $dms        = $convert->toDegreesMinutesSeconds();

        return new String($dms);
    }

    /**
     * Returns a decimal minutes representation of the coordinate
     *
     * @return String
     */
    public function toDecimalMinutes()
    {
        $coordinate = self::getBaseCoordinate($this);
        $convert    = new Convert($coordinate);
        $dm         = $convert->toDecimalMinutes();

        return new String($dm);
    }

    /**
     * Returns a Universal Transverse Mercator projection representation of the coordinate in meters
     *
     * @return String
     */
    public function toUniversalTransverseMercator()
    {
        $coordinate = self::getBaseCoordinate($this);
        $convert    = new Convert($coordinate);
        $utm        = $convert->toUniversalTransverseMercator();

        return new String($utm);
    }

    /**
     * Calculates the distance between two Coordinate objects
     *
     * @param  Coordinate      $coordinate
     * @param  DistanceUnit    $unit
     * @param  DistanceFormula $formula
     * @return Real
     */
    public function distanceFrom(Coordinate $coordinate, DistanceUnit $unit = null, DistanceFormula $formula = null)
    {
        if (null === $unit) {
            $unit = DistanceUnit::METER();
        }

        if (null === $formula) {
            $formula = DistanceFormula::FLAT();
        }

        $baseThis       = self::getBaseCoordinate($this);
        $baseCoordinate = self::getBaseCoordinate($coordinate);

        $distance = new Distance();
        $distance
            ->setFrom($baseThis)
            ->setTo($baseCoordinate)
            ->in($unit->getValue())
        ;

        $value = \call_user_func(array($distance, $formula->getValue()));

        return new Real($value);
    }

    /**
     * Returns a native string version of the Coordiantes object in format "$latitude,$longitude"
     *
     * @return string
     */
    public function __toString()
    {
        return \sprintf('%F,%F', $this->getLatitude()->getValue(), $this->getLongitude()->getValue());
    }

    /**
     * Returns the underlying Coordinate object
     *
     * @param  self           $coordinate
     * @return BaseCoordinate
     */
    protected static function getBaseCoordinate(self $coordinate)
    {
        $latitude   = $coordinate->getLatitude()->getValue();
        $longitude  = $coordinate->getLongitude()->getValue();
        $ellipsoid  = BaseEllipsoid::createFromName($coordinate->getEllipsoid()->getValue());
        $coordinate = new BaseCoordinate(array($latitude, $longitude), $ellipsoid);

        return $coordinate;
    }
}
