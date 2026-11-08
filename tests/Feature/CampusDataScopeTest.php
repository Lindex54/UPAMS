<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Campus;
use App\Models\Invoice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CampusDataScopeTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('campusScopedRoles')]
    public function test_campus_staff_only_query_operational_records_from_their_campus(string $roleName): void
    {
        $assignedCampus = Campus::factory()->create();
        $otherCampus = Campus::factory()->create();
        $assignedInvoice = Invoice::factory()->create(['campus_id' => $assignedCampus->id]);
        Invoice::factory()->create(['campus_id' => $otherCampus->id]);
        $assignedAsset = Asset::factory()->create(['campus_id' => $assignedCampus->id]);
        Asset::factory()->create(['campus_id' => $otherCampus->id]);
        $staffMember = $this->staffMember($roleName, $assignedCampus);

        $this->actingAs($staffMember);

        $this->assertSame([$assignedCampus->id], Campus::query()->pluck('id')->all());
        $this->assertSame([$assignedInvoice->id], Invoice::query()->pluck('id')->all());
        $this->assertSame([$assignedAsset->id], Asset::query()->pluck('id')->all());
    }

    public function test_campus_staff_cannot_open_an_invoice_from_another_campus(): void
    {
        $assignedCampus = Campus::factory()->create();
        $otherCampus = Campus::factory()->create();
        $otherCampusInvoice = Invoice::factory()->create(['campus_id' => $otherCampus->id]);
        $financeOfficer = $this->staffMember('Finance Officer', $assignedCampus);

        $this->actingAs($financeOfficer)
            ->get(route('billing.show', $otherCampusInvoice))
            ->assertNotFound();
    }

    public function test_new_operational_records_are_forced_to_the_staff_members_campus(): void
    {
        $assignedCampus = Campus::factory()->create();
        $otherCampus = Campus::factory()->create();
        $financeOfficer = $this->staffMember('Finance Officer', $assignedCampus);

        $this->actingAs($financeOfficer);

        $invoice = Invoice::factory()->create(['campus_id' => $otherCampus->id]);

        $this->assertSame($assignedCampus->id, $invoice->campus_id);
    }

    #[DataProvider('unrestrictedRoles')]
    public function test_university_wide_roles_can_query_records_from_every_campus(string $roleName): void
    {
        $firstCampus = Campus::factory()->create();
        $secondCampus = Campus::factory()->create();
        Invoice::factory()->create(['campus_id' => $firstCampus->id]);
        Invoice::factory()->create(['campus_id' => $secondCampus->id]);
        $staffMember = $this->staffMember($roleName, $firstCampus);

        $this->actingAs($staffMember);

        $this->assertCount(2, Invoice::query()->get());
    }

    public function test_campus_scoped_staff_without_an_assigned_campus_cannot_query_operational_records(): void
    {
        Invoice::factory()->create();
        $financeOfficer = User::factory()->create([
            'campus_id' => null,
            'role_id' => Role::factory()->create(['name' => 'Finance Officer'])->id,
        ]);

        $this->actingAs($financeOfficer);

        $this->assertSame(0, Invoice::query()->count());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function campusScopedRoles(): array
    {
        return [
            'estates manager' => ['University Property / Estates Manager'],
            'campus property officer' => ['Campus Property Officer'],
            'finance officer' => ['Finance Officer'],
            'IT technician' => ['IT Technician'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function unrestrictedRoles(): array
    {
        return [
            'system administrator' => ['System Administrator'],
            'university management' => ['University Management'],
        ];
    }

    private function staffMember(string $roleName, Campus $campus): User
    {
        return User::factory()->create([
            'campus_id' => $campus->id,
            'role_id' => Role::factory()->create(['name' => $roleName])->id,
        ]);
    }
}
