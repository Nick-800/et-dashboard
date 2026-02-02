<?php

namespace Database\Seeders;

use App\Models\OrganizationNode;
use Illuminate\Database\Seeder;

class OrganizationNodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        OrganizationNode::truncate();

        // Create Executive Leadership (Root)
        $executive = OrganizationNode::create([
            'title' => 'Executive Leadership',
            'names' => ['John Doe', 'Jane Smith'],
            'type' => 'leadership',
            'order' => 0,
            'is_active' => true,
        ]);

        // Create Engineering Department
        $engineering = OrganizationNode::create([
            'parent_id' => $executive->id,
            'title' => 'Engineering',
            'names' => ['Bob Johnson'],
            'type' => 'department',
            'order' => 0,
            'is_active' => true,
        ]);

        // Engineering sub-departments
        OrganizationNode::create([
            'parent_id' => $engineering->id,
            'title' => 'Frontend Team',
            'names' => ['Alice Cooper', 'Charlie Brown'],
            'type' => 'team',
            'order' => 0,
            'is_active' => true,
        ]);

        OrganizationNode::create([
            'parent_id' => $engineering->id,
            'title' => 'Backend Team',
            'names' => ['David Lee', 'Emma Watson'],
            'type' => 'team',
            'order' => 1,
            'is_active' => true,
        ]);

        OrganizationNode::create([
            'parent_id' => $engineering->id,
            'title' => 'DevOps Team',
            'names' => ['Frank Miller'],
            'type' => 'team',
            'order' => 2,
            'is_active' => true,
        ]);

        // Create Marketing Department
        $marketing = OrganizationNode::create([
            'parent_id' => $executive->id,
            'title' => 'Marketing',
            'names' => ['Grace Hopper'],
            'type' => 'department',
            'order' => 1,
            'is_active' => true,
        ]);

        // Marketing sub-departments
        OrganizationNode::create([
            'parent_id' => $marketing->id,
            'title' => 'Digital Marketing',
            'names' => ['Henry Ford', 'Iris West'],
            'type' => 'sub-department',
            'order' => 0,
            'is_active' => true,
        ]);

        OrganizationNode::create([
            'parent_id' => $marketing->id,
            'title' => 'Content Team',
            'names' => ['Jack Ryan'],
            'type' => 'sub-department',
            'order' => 1,
            'is_active' => true,
        ]);

        // Create Sales Department
        $sales = OrganizationNode::create([
            'parent_id' => $executive->id,
            'title' => 'Sales',
            'names' => ['Karen Page'],
            'type' => 'department',
            'order' => 2,
            'is_active' => true,
        ]);

        // Sales teams
        OrganizationNode::create([
            'parent_id' => $sales->id,
            'title' => 'Enterprise Sales',
            'names' => ['Liam Neeson', 'Maria Hill'],
            'type' => 'team',
            'order' => 0,
            'is_active' => true,
        ]);

        OrganizationNode::create([
            'parent_id' => $sales->id,
            'title' => 'SMB Sales',
            'names' => ['Nick Fury'],
            'type' => 'team',
            'order' => 1,
            'is_active' => true,
        ]);

        // Create HR Department
        $hr = OrganizationNode::create([
            'parent_id' => $executive->id,
            'title' => 'Human Resources',
            'names' => ['Olivia Pope'],
            'type' => 'department',
            'order' => 3,
            'is_active' => true,
        ]);

        // HR sub-departments
        OrganizationNode::create([
            'parent_id' => $hr->id,
            'title' => 'Recruitment',
            'names' => ['Peter Parker'],
            'type' => 'sub-department',
            'order' => 0,
            'is_active' => true,
        ]);

        OrganizationNode::create([
            'parent_id' => $hr->id,
            'title' => 'Employee Relations',
            'names' => ['Quinn Fabray'],
            'type' => 'sub-department',
            'order' => 1,
            'is_active' => true,
        ]);

        // Create Finance Department
        $finance = OrganizationNode::create([
            'parent_id' => $executive->id,
            'title' => 'Finance',
            'names' => ['Rachel Green'],
            'type' => 'department',
            'order' => 4,
            'is_active' => true,
        ]);

        // Finance teams
        OrganizationNode::create([
            'parent_id' => $finance->id,
            'title' => 'Accounting',
            'names' => ['Steve Rogers', 'Tony Stark'],
            'type' => 'team',
            'order' => 0,
            'is_active' => true,
        ]);

        OrganizationNode::create([
            'parent_id' => $finance->id,
            'title' => 'Financial Planning',
            'names' => ['Uma Thurman'],
            'type' => 'team',
            'order' => 1,
            'is_active' => true,
        ]);

        // Create an inactive node for testing
        OrganizationNode::create([
            'parent_id' => $executive->id,
            'title' => 'Legacy Department',
            'names' => ['Victor Von Doom'],
            'type' => 'department',
            'order' => 5,
            'is_active' => false,
        ]);

        $this->command->info('Organization structure seeded successfully!');
        $this->command->info('Total nodes created: ' . OrganizationNode::count());
        $this->command->info('Active nodes: ' . OrganizationNode::active()->count());
    }
}
