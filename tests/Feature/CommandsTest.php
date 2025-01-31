<?php

use Illuminate\Support\Facades\Cache;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardRemove;

it('sends /start command', function () {
    bot()
        ->hearText('/start')
        ->reply()
        ->assertReplyText(message('start'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.start',
    ]);
});

it('sends /help command', function () {
    bot()
        ->hearText('/help')
        ->reply()
        ->assertReplyText(message('start'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.help',
    ]);
});

it('sends /about command', function () {
    bot()
        ->hearText('/about')
        ->reply()
        ->assertReplyText(message('about'));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.about',
    ]);
});

it('sends /stats command with filled content', function () {
    $stats = [
        'stickers_optimized' => [
            'today' => 0,
            'yesterday' => 0,
            'last_7_days' => 0,
            'last_30_days' => 0,
            'year' => 0,
            'total' => 0,
            'last_update' => '0',
        ],
        'videos_optimized' => [
            'today' => 0,
            'yesterday' => 0,
            'last_7_days' => 0,
            'last_30_days' => 0,
            'year' => 0,
            'total' => 0,
            'last_update' => '0',
        ],
        'active_users' => [
            'today' => 0,
            'yesterday' => 0,
            'last_7_days' => 0,
            'last_30_days' => 0,
            'year' => 0,
            'total' => null,
            'last_update' => '0',
        ],
        'users' => [
            'today' => 0,
            'yesterday' => 0,
            'last_7_days' => 0,
            'last_30_days' => 0,
            'year' => 0,
            'total' => 0,
            'last_update' => '0',
        ],
    ];

    Cache::put('stats.stickers_optimized', $stats['stickers_optimized']);
    Cache::put('stats.videos_optimized', $stats['videos_optimized']);
    Cache::put('stats.active_users', $stats['active_users']);
    Cache::put('stats.users', $stats['users']);

    bot()
        ->hearText('/stats')
        ->reply()
        ->assertReplyText(message('stats.template', [
            'title' => __('stats.category.optimized.stickers'),
            ...$stats['stickers_optimized'],
        ]));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.stats',
    ]);

    Cache::forget('stats');
});

it('sends /stats command with empty content', function () {
    bot()
        ->hearText('/stats')
        ->reply()
        ->assertReplyText(message('stats.empty', [
            'title' => __('stats.category.optimized.stickers'),
        ]));

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.stats',
    ]);
});

it('sends /privacy command', function () {
    bot()
        ->hearText('/privacy')
        ->reply()
        ->assertReplyMessage([
            'text' => message('privacy'),
            'parse_mode' => ParseMode::HTML,
            'disable_web_page_preview' => true,
            'reply_markup' => InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make(trans('privacy.title'), config('bot.privacy'))),
        ]);

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.privacy',
    ]);
});

it('sends /cancel command', function () {
    bot()
        ->hearUpdateType(UpdateType::MESSAGE, [
            'text' => '/cancel',
            'from' => ['id' => 123],
            'chat' => ['id' => 321],
        ])
        ->reply()
        ->assertNoConversation(123, 321)
        ->assertCalled('sendMessage')
        ->assertReplyMessage([
            'text' => 'Removing keyboard...',
            'reply_markup' => ReplyKeyboardRemove::make(true),
        ])
        ->assertCalled('deleteMessage');

    $this->assertDatabaseHas('statistics', [
        'action' => 'command.cancel',
    ]);
});
