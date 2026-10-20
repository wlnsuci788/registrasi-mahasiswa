<?php

declare(strict_types=1);

namespace app\tests\Acceptance;

use app\tests\Support\AcceptanceTester;

final class HomeCest
{
    public function ensureThatHomePageWorks(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Portal Mahasiswa Asing', 'h1');
        $I->dontSee('Build with Yii Framework');
    }
}
