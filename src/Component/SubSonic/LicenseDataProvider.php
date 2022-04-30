<?php

declare(strict_types=1);

namespace Uxmp\Core\Component\SubSonic;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Usox\HyperSonic\FeatureSet\V1161\Contract\LicenseDataProviderInterface;

final class LicenseDataProvider implements LicenseDataProviderInterface
{
    public function isValid(): bool
    {
        return true;
    }

    public function getEmailAddress(): string
    {
        return 'foo@bar.com';
    }

    public function getExpiryDate(): DateTimeInterface
    {
        return (new DateTime())->add(DateInterval::createFromDateString('1 year'));
    }
}
