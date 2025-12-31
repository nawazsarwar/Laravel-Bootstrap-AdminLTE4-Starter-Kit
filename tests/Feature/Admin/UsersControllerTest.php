<?php

namespace Tests\Feature\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

test('admin can view users index', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_access']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $response = $this->get(route('admin.users.index'));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.users.index');
});

test('admin can view users index via ajax datatables', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_access']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    User::factory()->count(3)->create();
    
    $response = $this->getJson(route('admin.users.index'), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data',
    ]);
});

test('admin can view create user form', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $response = $this->get(route('admin.users.create'));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.users.create');
});

test('admin can create user', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $role = Role::factory()->create();
    
    $response = $this->post(route('admin.users.store'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'roles' => [$role->id],
    ]);
    
    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
        'name' => 'New User',
    ]);
    
    $user = User::where('email', 'newuser@example.com')->first();
    expect($user->roles)->toHaveCount(1);
});

test('admin can view edit user form', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $user = User::factory()->create();
    
    $response = $this->get(route('admin.users.edit', $user));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.users.edit');
});

test('admin can update user', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_edit']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $user = User::factory()->create();
    $role = Role::factory()->create();
    
    $response = $this->put(route('admin.users.update', $user), [
        'name' => 'Updated Name',
        'email' => $user->email,
        'roles' => [$role->id],
    ]);
    
    $response->assertRedirect(route('admin.users.index'));
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->roles->first()->id)->toBe($role->id);
});

test('admin can view user', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_show']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $user = User::factory()->create();
    
    $response = $this->get(route('admin.users.show', $user));
    
    $response->assertStatus(200);
    $response->assertViewIs('admin.users.show');
});

test('admin can delete user', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_delete']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $user = User::factory()->create();
    
    $response = $this->delete(route('admin.users.destroy', $user));
    
    $response->assertRedirect();
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

test('admin can mass delete users', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_delete']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $users = User::factory()->count(3)->create();
    $ids = $users->pluck('id')->toArray();
    
    $response = $this->delete(route('admin.users.massDestroy'), [
        'ids' => $ids,
    ]);
    
    $response->assertStatus(204);
    foreach ($users as $user) {
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
});

test('user without permission cannot access users index', function () {
    $user = $this->actingAsUser();
    
    $response = $this->get(route('admin.users.index'));
    
    $response->assertStatus(403);
});

test('user without permission cannot create user', function () {
    $user = $this->actingAsUser();
    
    $response = $this->get(route('admin.users.create'));
    
    $response->assertStatus(403);
});

test('admin can parse csv import', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $csvContent = "name,email,password\nTest User,test@example.com,password123";
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('users.csv', $csvContent);
    
    $response = $this->post(route('admin.users.parseCsvImport'), [
        'csv_file' => $file,
        'model' => 'User',
        'header' => true,
    ]);
    
    $response->assertStatus(200);
    $response->assertViewIs('csvImport.parseInput');
});

test('admin can process csv import', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $csvContent = "name,email,password\nTest User,test@example.com,password123";
    $filename = 'test_' . \Illuminate\Support\Str::random(10) . '.csv';
    $path = storage_path('app/csv_import/' . $filename);
    \Illuminate\Support\Facades\File::ensureDirectoryExists(storage_path('app/csv_import'));
    \Illuminate\Support\Facades\File::put($path, $csvContent);
    
    $response = $this->post(route('admin.users.processCsvImport'), [
        'filename' => $filename,
        'modelName' => 'User',
        'hasHeader' => true,
        'fields' => ['name' => 0, 'email' => 1, 'password' => 2],
        'redirect' => route('admin.users.index'),
    ]);
    
    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);
});

