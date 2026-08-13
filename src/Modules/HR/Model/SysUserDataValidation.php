<?php

declare(strict_types=1);

namespace Modules\HR\Model;

use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\InRange;

final class SysUserDataValidation
{
    public function __construct(

        #[Required]
        #[Length(min: 1, max: 50)]
        public ?string $firstName = null,

        #[Required]
        #[Length(min: 1, max: 50)]
        public ?string $lastName = null,

        #[Required]
        #[Length(min: 1, max: 50)]
        public ?string $middleName = null,

        #[Required]
        public ?string $birthday = null,

        #[Required]
        #[InRange(['male', 'female'])]
        public ?string $sex = null,

        #[Required]
        #[Length(min: 1, max: 100)]
        public ?string $birthPlace = null,


        #[Required]
        #[Length(min: 3, max: 255)]
        public ?string $username = null,

        #[Required]
        #[Length(min: 6)]
        public ?string $password = null,

        #[Required]
        public ?string $passwordConfirm = null,
    ) {
    }
}