<?php

namespace Tests\Feature;

use App\Telegram\Exceptions\UserBlockedException;
use App\Telegram\Exceptions\UserDeactivatedException;
use App\Jobs\SendNews;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;

uses(DatabaseTransactions::class);

it('dispatches job', function () {
    Queue::fake();
    SendNews::dispatch(12345689, 123);
    Queue::assertPushedOn('news', SendNews::class);
});

it('runs job', function () {
    $this->mock(Nutgram::class)
        ->shouldReceive('forwardMessage')
        ->andReturn(new Message(bot()));

    SendNews::dispatch($this->chat->chat_id, 123);

    $this->assertDatabaseHas('chats', ['chat_id' => $this->chat->chat_id]);
});

it('throws UserDeactivatedException', function () {
    $this->mock(Nutgram::class)
        ->shouldReceive('forwardMessage')
        ->andThrowExceptions([new UserDeactivatedException('Forbidden: user is deactivated')]);

    SendNews::dispatch($this->chat->chat_id, 123);

    $this->assertDatabaseMissing('chats', ['chat_id' => $this->chat->chat_id]);
});

it('throws UserBlockedException', function () {
    $this->mock(Nutgram::class)
        ->shouldReceive('forwardMessage')
        ->andThrowExceptions([new UserBlockedException('Forbidden: bot was blocked by the user')]);

    SendNews::dispatch($this->chat->chat_id, 123);

    $this->assertDatabaseHas('chats', ['chat_id' => $this->chat->chat_id]);
});

it('throws Exception', function () {
    $this->mock(Nutgram::class)
        ->shouldReceive('forwardMessage')
        ->andThrowExceptions([new Exception('Another exception')]);

    SendNews::dispatch($this->chat->chat_id, 123);

    $this->assertDatabaseHas('chats', ['chat_id' => $this->chat->chat_id]);
})->throws(Exception::class, 'Another exception');
