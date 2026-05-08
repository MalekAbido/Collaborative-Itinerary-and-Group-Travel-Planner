<?php

use PHPUnit\Framework\TestCase;
use App\Models\AttendanceMember;
use Core\Database;

class AttendanceMemberTest extends TestCase
{
    protected function setUp(): void
    {
        Database::$nextResult = [];
        Database::$resultsQueue = [];
    }

    public function testGetAllByListIdPaths()
    {
        // Path 1: Itinerary not found (skips sync logic)
        Database::$resultsQueue = [
            [], // No Itinerary ID returned
            []  // No members list returned
        ];
        $members = AttendanceMember::getAllByListId(1);
        $this->assertIsArray($members);
        $this->assertEmpty($members);

        // Path 2: Itinerary found (performs sync)
        Database::$resultsQueue = [
            [55], // Itinerary ID
            // No result needed for the INSERT execute() as it doesn't call fetch
            [
                ['id' => 1, 'status' => 'Going', 'note' => 'Excited!', 'attendanceListId' => 1, 'tripMemberId' => 10],
                ['id' => 2, 'status' => 'Pending', 'note' => null, 'attendanceListId' => 1, 'tripMemberId' => 11],
            ]
        ];

        $members = AttendanceMember::getAllByListId(1);
        
        $this->assertCount(2, $members);
        $this->assertEquals('Going', $members[0]->getStatus());
        $this->assertEquals(10, $members[0]->getTripMemberId());
        $this->assertEquals('Pending', $members[1]->getStatus());
        $this->assertEquals(11, $members[1]->getTripMemberId());
    }
}
