<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);
});

test('user can view their profile', function () {
    $response = $this->actingAs($this->user)->get(route('profile.show'));

    $response->assertOk();
    $response->assertInertia(
        fn($assert) => $assert
            ->component('Profile/Show')
            ->has('user')
            ->where('user.name', $this->user->name)
            ->where('user.email', $this->user->email)
    );
});

test('user can view profile edit page', function () {
    $response = $this->actingAs($this->user)->get(route('profile.edit'));

    $response->assertOk();
    $response->assertInertia(
        fn($assert) => $assert
            ->component('Profile/Edit')
            ->has('user')
    );
});

test('user can update profile information', function () {
    $response = $this->actingAs($this->user)->put(route('profile.update'), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'phone' => '+2348012345678',
    ]);

    $response->assertRedirect(route('profile.show'));

    $this->user->refresh();
    expect($this->user->name)->toBe('Updated Name')
        ->and($this->user->email)->toBe('updated@example.com')
        ->and($this->user->phone)->toBe('+2348012345678');
});

test('profile update validates required fields', function () {
    $response = $this->actingAs($this->user)->put(route('profile.update'), [
        'name' => '',
        'email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['name', 'email']);
});

test('user can upload avatar', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->actingAs($this->user)->post(route('profile.avatar'), [
        'avatar' => $file,
    ]);

    $response->assertRedirect();

    $this->user->refresh();
    expect($this->user->avatar)->not->toBeNull();
    Storage::disk('public')->assertExists($this->user->avatar);
});

test('avatar upload validates file type', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->actingAs($this->user)->post(route('profile.avatar'), [
        'avatar' => $file,
    ]);

    $response->assertSessionHasErrors('avatar');
});

test('avatar upload validates file size', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('large.jpg')->size(3000); // 3MB

    $response = $this->actingAs($this->user)->post(route('profile.avatar'), [
        'avatar' => $file,
    ]);

    $response->assertSessionHasErrors('avatar');
});

test('user can delete avatar', function () {
    Storage::fake('public');

    // Upload an avatar first
    $file = UploadedFile::fake()->image('avatar.jpg');
    $path = $file->store('avatars', 'public');
    $this->user->update(['avatar' => $path]);

    // Delete it
    $response = $this->actingAs($this->user)->delete(route('profile.avatar.delete'));

    $response->assertRedirect();

    $this->user->refresh();
    expect($this->user->avatar)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('user can change password with correct current password', function () {
    $response = $this->actingAs($this->user)->put(route('profile.password'), [
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();

    $this->user->refresh();
    expect(Hash::check('newpassword123', $this->user->password))->toBeTrue();
});

test('user cannot change password with incorrect current password', function () {
    $response = $this->actingAs($this->user)->put(route('profile.password'), [
        'current_password' => 'wrongpassword',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors('current_password');
});

test('password change requires confirmation', function () {
    $response = $this->actingAs($this->user)->put(route('profile.password'), [
        'current_password' => 'password123',
        'password' => 'newpassword123',
        'password_confirmation' => 'different123',
    ]);

    $response->assertSessionHasErrors('password');
});

test('password must be at least 8 characters', function () {
    $response = $this->actingAs($this->user)->put(route('profile.password'), [
        'current_password' => 'password123',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors('password');
});

test('guest cannot access profile routes', function () {
    $this->get(route('profile.show'))->assertRedirect(route('login'));
    $this->get(route('profile.edit'))->assertRedirect(route('login'));
    $this->put(route('profile.update'), [])->assertRedirect(route('login'));
});
