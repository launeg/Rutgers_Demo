<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../lib/data.php';

final class FilterTest extends TestCase
{
    public function testNoFiltersAddNothing(): void
    {
        $this->assertSame(['', '', []], form_filter_sql([]));
        $this->assertSame(['', '', []], form_filter_sql(['q' => '  ', 'status' => '', 'system' => '', 'role' => '', 'department' => '']));
    }

    public function testSearchMatchesNameNetidAndEmail(): void
    {
        [$sql, $types, $params] = form_filter_sql(['q' => ' casey ']);
        $this->assertStringContainsString('f.userNetID LIKE ?', $sql);
        $this->assertStringContainsString('f.userEmail LIKE ?', $sql);
        $this->assertSame('sss', $types);
        $this->assertSame(['%casey%', '%casey%', '%casey%'], $params);
    }

    public function testSearchWildcardsAreLiteral(): void
    {
        [, , $params] = form_filter_sql(['q' => '50%_off\\']);
        $this->assertSame('%50\\%\\_off\\\\%', $params[0]);
    }

    public function testStatusMustBeKnown(): void
    {
        $this->assertSame([' AND f.status = ?', 's', ['approved']], form_filter_sql(['status' => 'approved']));
        $this->assertSame(['', '', []], form_filter_sql(['status' => 'bogus']));
    }

    public function testSystemAndRoleUseExists(): void
    {
        [$sql, $types, $params] = form_filter_sql(['system' => '4', 'role' => '25']);
        $this->assertSame(2, substr_count($sql, 'EXISTS'));
        $this->assertSame('ii', $types);
        $this->assertSame([4, 25], $params);
    }

    public function testIdsMustBePositiveIntegers(): void
    {
        foreach (['0', '-1', 'abc', '1 OR 1=1', '2.5'] as $bad) {
            $this->assertSame(['', '', []], form_filter_sql(['department' => $bad, 'system' => $bad, 'role' => $bad]));
        }
    }

    public function testFiltersCombine(): void
    {
        [$sql, $types, $params] = form_filter_sql(['q' => 'x', 'status' => 'denied', 'department' => '2']);
        $this->assertSame(3, substr_count($sql, ' AND '));
        $this->assertSame('ssssi', $types);
        $this->assertSame(['%x%', '%x%', '%x%', 'denied', 2], $params);
    }
}
