<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../lib/workflow.php';

final class WorkflowTest extends TestCase
{
    public function testApproveMovesThroughEveryStage(): void
    {
        $this->assertSame('pending_role_manager', next_status('pending_supervisor', 'approve'));
        $this->assertSame('pending_it', next_status('pending_role_manager', 'approve'));
        $this->assertSame('approved', next_status('pending_it', 'approve'));
    }

    public function testDenyWorksAtEveryStage(): void
    {
        foreach (['pending_supervisor', 'pending_role_manager', 'pending_it'] as $status) {
            $this->assertSame('denied', next_status($status, 'deny'));
        }
    }

    public function testFinishedRequestsCannotMove(): void
    {
        foreach (['approved', 'denied'] as $status) {
            $this->assertNull(next_status($status, 'approve'));
            $this->assertNull(next_status($status, 'deny'));
        }
    }

    public function testUnknownActionIsRejected(): void
    {
        $this->assertNull(next_status('pending_supervisor', 'delete'));
        $this->assertNull(next_status('pending_supervisor', ''));
    }

    public function testRoleOnlyActsOnItsOwnStage(): void
    {
        $form = ['status' => 'pending_role_manager', 'supervisorUserID' => 3];
        $this->assertTrue(can_act(['userID' => 4, 'userRole' => 'role_manager'], $form));
        $this->assertFalse(can_act(['userID' => 5, 'userRole' => 'it_admin'], $form));
        $this->assertFalse(can_act(['userID' => 3, 'userRole' => 'supervisor'], $form));
    }

    public function testSubmitterCanNeverAct(): void
    {
        $form = ['status' => 'pending_supervisor', 'supervisorUserID' => 1];
        $this->assertFalse(can_act(['userID' => 1, 'userRole' => 'submitter'], $form));
    }

    public function testSupervisorOnlyActsOnTheirOwnRequests(): void
    {
        $form = ['status' => 'pending_supervisor', 'supervisorUserID' => 3];
        $this->assertTrue(can_act(['userID' => 3, 'userRole' => 'supervisor'], $form));
        $this->assertFalse(can_act(['userID' => 9, 'userRole' => 'supervisor'], $form));
    }

    public function testFinishedRequestHasNoActor(): void
    {
        foreach (['it_admin', 'role_manager', 'supervisor'] as $role) {
            $this->assertFalse(can_act(['userID' => 3, 'userRole' => $role], ['status' => 'approved', 'supervisorUserID' => 3]));
        }
    }

    public function testQueueStatusPerRole(): void
    {
        $this->assertSame('pending_supervisor', queue_status_for_role('supervisor'));
        $this->assertSame('pending_it', queue_status_for_role('it_admin'));
        $this->assertNull(queue_status_for_role('submitter'));
    }
}
