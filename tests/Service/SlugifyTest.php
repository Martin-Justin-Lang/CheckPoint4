<?php


namespace App\Tests\Service;


use App\Service\Slugify;

class SlugifyTest extends \PHPUnit\Framework\TestCase
{
    public function testSlugify()
    {
        $slugify = new Slugify();
        $this->assertEquals('et-amet-commodi-nam-a-modi', $slugify->generate('Et amet commodi nam a modi.'));
    }
}
