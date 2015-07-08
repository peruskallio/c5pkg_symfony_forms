<?php

namespace Mainio\C5\Symfony\Form\Extension\Concrete5\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataTransformer\BaseDateTimeTransformer;

/**
 * Transforms between a normalized time and a concrete5 widget format.
 *
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 */
class DateTimeToFormWidgetTransformer extends BaseDateTimeTransformer
{

    /**
     * Constructor.
     *
     * @param string $inputTimezone  The input timezone
     * @param string $outputTimezone The output timezone
     *
     * @throws UnexpectedTypeException if a timezone is not a string
     */
    public function __construct($inputTimezone = null, $outputTimezone = null)
    {
        parent::__construct($inputTimezone, $outputTimezone);
    }

    /**
     * Transforms a normalized date into a the concrete5 datetime widget format.
     *
     * @param \DateTime $dateTime Normalized date.
     *
     * @return string Widget format date.
     *
     * @throws TransformationFailedException If the given value is not an
     *                                       instance of \DateTime or if the
     *                                       output timezone is not supported.
     */
    public function transform($dateTime)
    {
        if ($dateTime === null || trim($dateTime) == '') {
            return '';
        }

        if (!$dateTime instanceof \DateTime) {
            throw new TransformationFailedException('Expected a \DateTime.');
        }

        $dateTime = clone $dateTime;
        if ($this->inputTimezone !== $this->outputTimezone) {
            try {
                $dateTime->setTimezone(new \DateTimeZone($this->outputTimezone));
            } catch (\Exception $e) {
                throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
            }
        }

        // This is passed to the concrete5 DateTime widget, so the format
        // can be anything that can be parsed with the default PHP datetime
        // functions.
        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * Transforms a localized date into a normalized date.
     *
     * @param array $value Localized date
     *
     * @return \DateTime Normalized date
     *
     * @throws TransformationFailedException If the given value is not an array,
     *                                       if the value could not be transformed
     *                                       or if the input timezone is not
     *                                       supported.
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        if ('' === implode('', $value)) {
            return;
        }

        if (isset($value['dt']) && !preg_match('/^[\d]{4}-[\d]{2}-[\d]{2}$/', $value['dt'])) {
            throw new TransformationFailedException('The date time value is invalid');
        }

        if (isset($value['h']) && !ctype_digit((string) $value['h'])) {
            throw new TransformationFailedException('The hour is invalid');
        }

        if (isset($value['m']) && !ctype_digit((string) $value['m'])) {
            throw new TransformationFailedException('The minute is invalid');
        }

        $dt = $value['dt'];
        $h = intval($value['h']);
        $m = intval($value['m']);

        $dh = \Core::make('helper/date');

        if ($dh->getTimeFormat() == 12) {
            if (isset($value['a']) && $value['a'] == 'PM') {
                $h += 12;
            }
        }

        try {
            $dateTime = new \DateTime(sprintf(
                '%s %s:%s:00 %s',
                empty($dt) ? '1970-01-01' : $dt,
                empty($h) ? '00' : $h,
                empty($m) ? '00' : $m,
                $this->outputTimezone
            ));

            if ($this->inputTimezone !== $this->outputTimezone) {
                $dateTime->setTimezone(new \DateTimeZone($this->inputTimezone));
            }
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return $dateTime;
    }
}
