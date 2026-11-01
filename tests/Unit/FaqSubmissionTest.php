<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\FaqSubmission;
use PHPUnit\Framework\TestCase;

final class FaqSubmissionTest extends TestCase
{
    public function test_rejected_can_resubmit(): void
    {
        $submission = new FaqSubmission();
        $submission->status = 'rejected';
        $this->assertTrue($submission->canResubmit());
        $this->assertFalse($submission->canWithdraw());
    }

    public function test_pending_cannot_resubmit(): void
    {
        $submission = new FaqSubmission();
        $submission->status = 'pending';
        $this->assertFalse($submission->canResubmit());
        $this->assertTrue($submission->canWithdraw());
    }
}
