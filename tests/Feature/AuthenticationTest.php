<?php

use App\Models\User;

test('user can register with valid credentials', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '+2348012345678'
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User'
    ]);
    $this->assertAuthenticated();
});

test('user cannot register with existing email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);

    $response->assertSessionHasErrors('email');
});

test('user can login with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123')
    ]);

    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password123'
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with incorrect password', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123')
    ]);

    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'wrongpassword'
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('user cannot login with non-existent email', function () {
    $response = $this->post(route('login'), [
        'email' => 'nonexistent@example.com',
        'password' => 'password123'
    ]);

    $response->assertSessionHasErrors();
    $this->assertGuest();
});

test('user can logout successfully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('logout'));

    $response->assertRedirect('/');
    $this->assertGuest();
});

test('guest cannot access authenticated routes', function () {
    $response = $this->get(route('bookings.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated user can access protected routes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('bookings.index'));

    $response->assertOk();
});

test('registration requires password confirmation', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123'
    ]);

    $response->assertSessionHasErrors('password');
});

test('registration requires valid email format', function () {
    $response = $this->post(route('register'), [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);

    $response->assertSessionHasErrors('email');
});
