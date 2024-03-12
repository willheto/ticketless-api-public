<?php

namespace Tests;

use App\Models\Chat;


class ChatModelTest extends TestCase
{

    public function testGetValidationRules()
    {
        $rules = Chat::getValidationRules([]);
        $this->assertIsArray($rules);
    }
}
