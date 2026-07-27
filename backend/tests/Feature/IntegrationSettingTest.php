<?php

use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Crypt;

test('getValue returns null for missing key', function () {
    expect(IntegrationSetting::getValue('non_existent_key'))->toBeNull();
    expect(IntegrationSetting::getValue('non_existent_key', 'fallback'))->toBe('fallback');
});

test('setValue stores a plain value and getValue retrieves it', function () {
    IntegrationSetting::setValue('telegram_chat_id', '123456789');

    expect(IntegrationSetting::getValue('telegram_chat_id'))->toBe('123456789');
});

test('setValue stores bot_token encrypted and getValue decrypts it', function () {
    $token = 'abc123:ABCdef_very_secret_token';
    IntegrationSetting::setValue('telegram_bot_token', $token);

    $record = IntegrationSetting::where('key', 'telegram_bot_token')->first();

    expect($record)->not->toBeNull();
    expect($record->is_encrypted)->toBeTrue();
    expect($record->value)->not->toBe($token); // stored encrypted
    expect(Crypt::decryptString($record->value))->toBe($token);
    expect(IntegrationSetting::getValue('telegram_bot_token'))->toBe($token);
});

test('setValue with null or empty clears the value', function () {
    IntegrationSetting::setValue('telegram_chat_id', '999');
    IntegrationSetting::setValue('telegram_chat_id', null);

    expect(IntegrationSetting::getValue('telegram_chat_id'))->toBeNull();
});

test('setValue updates existing key', function () {
    IntegrationSetting::setValue('telegram_enabled', '0');
    IntegrationSetting::setValue('telegram_enabled', '1');

    expect(IntegrationSetting::getValue('telegram_enabled'))->toBe('1');
    expect(IntegrationSetting::where('key', 'telegram_enabled')->count())->toBe(1);
});

test('getMany retrieves multiple keys at once', function () {
    IntegrationSetting::setValue('telegram_enabled', '1');
    IntegrationSetting::setValue('telegram_chat_id', '987654321');

    $result = IntegrationSetting::getMany(['telegram_enabled', 'telegram_chat_id', 'missing_key']);

    expect($result['telegram_enabled'])->toBe('1');
    expect($result['telegram_chat_id'])->toBe('987654321');
    expect($result['missing_key'])->toBeNull();
});

test('setMany sets multiple keys at once', function () {
    IntegrationSetting::setMany([
        'telegram_enabled' => '1',
        'telegram_chat_id' => '111222333',
    ]);

    expect(IntegrationSetting::getValue('telegram_enabled'))->toBe('1');
    expect(IntegrationSetting::getValue('telegram_chat_id'))->toBe('111222333');
});
