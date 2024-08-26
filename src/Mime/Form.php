<?php
/**
 * This file is part of VinChan.
 * @link     https://www.vinchan.cn
 * @contact  ademo@vip.qq.com
 * @license  https://www.vinchan.cn
 */
declare(strict_types=1);

namespace Vin\ObjectBuilder\Mime;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Form
{
    public function __construct($default = '') {}
}
