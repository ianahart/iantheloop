<?php

namespace App\Helpers;

use DateTime;
use DateTimeZone;

class FormattingUtil
{
  public static function blurb(string $text): string
  {
    $parts = explode(' ', $text);

    return count($parts) > 10 ? implode(' ', array_slice($parts, 0, 10)) . '...' : implode(' ', $parts);
  }

  public static function date(string $val): string
  {
    $time = new DateTime($val);

    $time->setTimezone(new DateTimeZone('America/New_York'));

    $formattedDate = $time->format('M j Y h:i a');

    return $formattedDate;
  }

  public static function capitalize(string $name): string
  {
    $parts = explode(' ', $name);

    return implode(' ', array_map(function ($part) {

      return strtoupper(substr($part, 0, 1)) . strtolower(substr($part, 1));
    }, $parts));
  }
}
