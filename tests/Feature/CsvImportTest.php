<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

test('admin can parse csv file for import', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $csvContent = "name,email,password\nJohn Doe,john@example.com,password123";
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('users.csv', $csvContent);
    
    $response = $this->post(route('admin.users.parseCsvImport'), [
        'csv_file' => $file,
        'model' => 'User',
        'header' => true,
    ]);
    
    $response->assertStatus(200);
    $response->assertViewIs('csvImport.parseInput');
    $response->assertViewHas('headers');
    $response->assertViewHas('modelName', 'User');
});

test('csv import validates model whitelist', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $csvContent = "name,email\nTest,test@example.com";
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('invalid.csv', $csvContent);
    
    $response = $this->post(route('admin.users.parseCsvImport'), [
        'csv_file' => $file,
        'model' => 'InvalidModel',
        'header' => true,
    ]);
    
    $response->assertSessionHasErrors('model');
});

test('csv import validates file type', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);
    
    $response = $this->post(route('admin.users.parseCsvImport'), [
        'csv_file' => $file,
        'model' => 'User',
        'header' => true,
    ]);
    
    $response->assertSessionHasErrors('csv_file');
});

test('admin can process csv import', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $csvContent = "name,email,password\nJohn Doe,john@example.com,password123\nJane Smith,jane@example.com,password456";
    $filename = 'test_' . \Illuminate\Support\Str::random(10) . '.csv';
    Storage::put("csv_import/{$filename}", $csvContent);
    
    $response = $this->post(route('admin.users.processCsvImport'), [
        'filename' => $filename,
        'modelName' => 'User',
        'hasHeader' => true,
        'fields' => ['name' => 0, 'email' => 1, 'password' => 2],
        'redirect' => route('admin.users.index'),
    ]);
    
    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
    $this->assertDatabaseHas('users', [
        'email' => 'jane@example.com',
    ]);
    
    expect(Storage::exists("csv_import/{$filename}"))->toBeFalse();
});

test('csv import validates fillable fields', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $csvContent = "name,email,invalid_field\nTest,test@example.com,value";
    $filename = 'test_' . \Illuminate\Support\Str::random(10) . '.csv';
    Storage::put("csv_import/{$filename}", $csvContent);
    
    $response = $this->post(route('admin.users.processCsvImport'), [
        'filename' => $filename,
        'modelName' => 'User',
        'hasHeader' => true,
        'fields' => ['name' => 0, 'email' => 1, 'invalid_field' => 2],
        'redirect' => route('admin.users.index'),
    ]);
    
    $response->assertStatus(400);
});

test('csv import handles files without headers', function () {
    $admin = $this->createAdminUser();
    $permission = Permission::firstOrCreate(['title' => 'user_create']);
    $admin->roles->first()->permissions()->attach($permission);
    $this->actingAs($admin);
    
    $csvContent = "John Doe,john@example.com,password123";
    $filename = 'test_' . \Illuminate\Support\Str::random(10) . '.csv';
    Storage::put("csv_import/{$filename}", $csvContent);
    
    $response = $this->post(route('admin.users.processCsvImport'), [
        'filename' => $filename,
        'modelName' => 'User',
        'hasHeader' => false,
        'fields' => ['name' => 0, 'email' => 1, 'password' => 2],
        'redirect' => route('admin.users.index'),
    ]);
    
    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
});

