<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightBundle\Tests;

use Dbp\Relay\GreenlightBundle\DataProvider\Utils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UtilsTest extends TestCase
{
    public function testSecurityByObscurity()
    {
        $token = 'eyJhbGciOiJQQkVTMi1IUzI1NitBMTI4S1ciLCJlbmMiOiJBMjU2R0NNIiwicDJjIjoyMzI2LCJwMnMiOiJZc1lYSEtSc193cWhBVmZwSWFVWnB3In0.YTGE3oIllJ5rRPG8bdQgd5g1qKlGYrLKEHU9a5ql4Qa_ytWaSYQ2wQ.zzicAquZYN_3GH5v.LwBUwTkb.HaVguTwlgGnLYOlfp2Y9rg';
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer test');
        $res = Utils::securityByObscurity($request, $token);
        $this->assertSame('foobar', $res);
    }

    public function testSecurityByObscurityFail()
    {
        $token = 'nope';
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer test');
        $this->expectException(AccessDeniedException::class);
        Utils::securityByObscurity($request, $token);
    }
}