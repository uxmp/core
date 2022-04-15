<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Usox\HyperSonic\FeatureSet\V1161\Contract\LicenseDataProviderInterface;

/**
 * Returns license information
 *
 * As this server does not implement licensing, we return some dummy data
 */
final class LicenseDataProvider implements LicenseDataProviderInterface
{
    public function isValid(): bool
    {
        return true;
    }

    public function getEmailAddress(): string
    {
        return 'no-licence-required@example.com';
    }

    public function getExpiryDate(): DateTimeInterface
    {
        return (new DateTime())->add(DateInterval::createFromDateString('1 year'));
    }
}
