<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../lib/data.php';

final class ValidateFormTest extends TestCase
{
    private array $options;

    protected function setUp(): void
    {
        $this->options = [
            'departments'   => [['id' => 1]],
            'requestTypes'  => [['id' => 1]],
            'employeeTypes' => [['id' => 1]],
            'supervisors'   => [['id' => 3]],
            'roles'         => [['id' => 7], ['id' => 8]],
        ];
    }

    private function valid(array $override = []): array
    {
        return $override + [
            'firstName' => 'Casey', 'lastName' => 'Newhire', 'netid' => 'cn5001', 'email' => 'cn@example.edu',
            'effDate' => '2030-01-15', 'departmentId' => '1', 'supervisorId' => '3',
            'requestTypeId' => '1', 'employeeTypeId' => '1', 'roleIds' => ['7'], 'justification' => 'Needs access',
            'approvalName' => 'Alex Submitter', 'approvalSignature' => 'Alex Submitter', 'approvalDate' => '2030-01-02',
        ];
    }

    public function testValidInputHasNoErrors(): void
    {
        $this->assertSame([], validate_form($this->valid(), $this->options));
    }

    public function testRequiredTextFields(): void
    {
        $errors = validate_form($this->valid(['firstName' => ' ', 'lastName' => '', 'justification' => '']), $this->options);
        $this->assertArrayHasKey('firstName', $errors);
        $this->assertArrayHasKey('lastName', $errors);
        $this->assertArrayHasKey('justification', $errors);
    }

    public function testApprovalSectionRequiredToSubmit(): void
    {
        $errors = validate_form($this->valid(['approvalName' => '', 'approvalSignature' => ' ', 'approvalDate' => '']), $this->options);
        $this->assertArrayHasKey('approvalName', $errors);
        $this->assertArrayHasKey('approvalSignature', $errors);
        $this->assertArrayHasKey('approvalDate', $errors);
    }

    public function testBadNetidEmailAndDate(): void
    {
        $errors = validate_form($this->valid(['netid' => 'no spaces!', 'email' => 'nope', 'effDate' => '2030-02-31']), $this->options);
        $this->assertArrayHasKey('netid', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('effDate', $errors);
    }

    public function testChoicesMustBeFromTheAvailableOptions(): void
    {
        $errors = validate_form($this->valid(['departmentId' => '99', 'supervisorId' => '', 'requestTypeId' => 'x']), $this->options);
        $this->assertArrayHasKey('departmentId', $errors);
        $this->assertArrayHasKey('supervisorId', $errors);
        $this->assertArrayHasKey('requestTypeId', $errors);
    }

    public function testRolesRequiredAndMustBeKnown(): void
    {
        $this->assertArrayHasKey('roleIds', validate_form($this->valid(['roleIds' => []]), $this->options));
        $this->assertArrayHasKey('roleIds', validate_form($this->valid(['roleIds' => ['7', '99']]), $this->options));
        $this->assertSame([], validate_form($this->valid(['roleIds' => ['7', '8']]), $this->options));
    }
}
